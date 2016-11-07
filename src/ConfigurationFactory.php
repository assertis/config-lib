<?php
declare(strict_types = 1);

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\AbstractLazyDriver;
use Assertis\Configuration\Drivers\DriverInterface;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class for creating configurations object
 *
 * @package Assertis\Configuration
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationFactory
{
    /**
     * @var DriverInterface
     */
    private $provider;

    /**
     * @var null|ValidatorInterface
     */
    private $validator;

    /**
     * @var ConfigurationArray[]
     */
    private $cache = [];

    /**
     * Default configuration key
     * @var string
     */
    const DEFAULT_KEY = self::ENV_DEV;

    const ENV_DEV = 'dev';
    const ENV_INTEGRATION = 'integration';
    const ENV_TEST = 'test';
    const ENV_UAT_SLEEPER = 'uat-sleeper';
    const ENV_PROD = 'prod';
    const ENV_COMMON = 'common';

    /**
     * ConfigurationFactory constructor.
     * @param DriverInterface $provider
     * @param null|ValidatorInterface $validator
     */
    public function __construct(DriverInterface $provider, ValidatorInterface $validator = null)
    {
        $this->provider = $provider;
        $this->validator = $validator;
    }

    /**
     * Load setting on key key
     *
     * @param string $key Main key of configuration
     * @param array $default
     * @param bool $isCached
     * @param Constraint|Constraint[]|null $constraints
     * @return ConfigurationArray
     */
    public function load($key = self::DEFAULT_KEY, array $default = [], $constraints = null, $isCached = true)
    {
        if ($isCached && isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $configuration = $this->doLoad($this->provider, $key, $default);

        if ($this->validator && $constraints && in_array($key, [self::ENV_DEV, self::ENV_TEST])) {
            self::assertMatchesSchema($configuration, $key, $this->validator, $constraints);
        }

        if ($isCached) {
            $this->cache[$key] = $configuration;
        }

        return $configuration;
    }

    /**
     * @param DriverInterface $provider
     * @param string $key
     * @param array $default
     * @return ConfigurationArray|LazyConfigurationArray
     */
    protected function doLoad(
        DriverInterface $provider,
        string $key = self::DEFAULT_KEY,
        array $default = []
    ): ConfigurationArray {
        return self::init($provider, $key, $default);
    }

    /**
     * Load configuration
     *
     * @param DriverInterface $provider
     * @param $key
     * @param array $default
     * @return ConfigurationArray
     * @throws Exception
     */
    public static function init(
        DriverInterface $provider,
        string $key = self::DEFAULT_KEY,
        array $default = []
    ) {
        //If configuration is lazy we can't validate structure or key
        if ($provider instanceof AbstractLazyDriver) {
            return new LazyConfigurationArray($provider);
        }

        self::assertConfigurationExists($provider, $key);

        $settings = $provider->getSettings($key);

        return new ConfigurationArray(array_merge_recursive($settings, $default));
    }

    /**
     * Validate configuration
     * @param ConfigurationArray $configuration
     * @param string $key
     * @param ValidatorInterface $validator
     * @param array $constraints
     * @throws ConfigurationNotFoundException
     */
    public static function assertMatchesSchema(
        ConfigurationArray $configuration,
        string $key,
        ValidatorInterface $validator,
        array $constraints
    ) {
        $violations = $validator->validate($configuration->getAll()->getArrayCopy(), $constraints);

        if (!$violations->count()) {
            return;
        }

        $error = "Validation errors:";

        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $error .= "\n";

            if (!empty($violation->getPropertyPath())) {
                $error .= "[" . $violation->getPropertyPath() . "]";
            }

            $error .= $violation->getMessage();
        }

        throw new ConfigurationNotValidException(sprintf(
            'Configuration %s has bad structure: %s',
            $key,
            $error
        ));
    }

    /**
     * Validate configuration array if we have key etc.
     *
     * @param DriverInterface $provider
     * @param string $key key for configuration
     * @throws ConfigurationNotFoundException
     */
    private static function assertConfigurationExists(DriverInterface $provider, $key)
    {
        if ($provider->keyExists($key)) {
            return;
        }

        throw new ConfigurationNotFoundException(sprintf(
            'Configuration %s not found in configuration object',
            $key
        ));
    }
}

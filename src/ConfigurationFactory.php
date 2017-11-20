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
    private $driver;

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
    const ENV_SID = 'sid';
    const ENV_STAGE = 'stage';
    const ENV_PROD = 'prod';
    const ENV_COMMON = 'common';

    /**
     * ConfigurationFactory constructor.
     * @param DriverInterface $driver
     * @param null|ValidatorInterface $validator
     */
    public function __construct(DriverInterface $driver, ValidatorInterface $validator = null)
    {
        $this->driver = $driver;
        $this->validator = $validator;
    }

    /**
     * Load setting on key key
     *
     * @param string $source Main key of configuration
     * @param array $default
     * @param bool $isCached
     * @param Constraint|Constraint[]|null $constraints
     * @return ConfigurationArray
     */
    public function load($source = self::DEFAULT_KEY, array $default = [], $constraints = null, $isCached = true)
    {
        if ($isCached && isset($this->cache[$source])) {
            return $this->cache[$source];
        }

        $configuration = $this->doLoad($this->driver, $source, $default);

        if ($this->validator && $constraints && in_array($source, [self::ENV_DEV, self::ENV_TEST])) {
            self::assertMatchesSchema($configuration, $source, $this->validator, $constraints);
        }

        if ($isCached) {
            $this->cache[$source] = $configuration;
        }

        return $configuration;
    }

    /**
     * @param DriverInterface $driver
     * @param string $source
     * @param array $default
     * @return ConfigurationArray|LazyConfigurationArray
     */
    protected function doLoad(
        DriverInterface $driver,
        string $source = self::DEFAULT_KEY,
        array $default = []
    ): ConfigurationArray {
        return self::init($driver, $source, $default);
    }

    /**
     * Load configuration
     *
     * @param DriverInterface $driver
     * @param string $source
     * @param array $default
     * @return ConfigurationArray
     * @throws Exception
     */
    public static function init(
        DriverInterface $driver,
        string $source = self::DEFAULT_KEY,
        array $default = []
    ) {
        //If configuration is lazy we can't validate structure or key
        if ($driver instanceof AbstractLazyDriver) {
            return new LazyConfigurationArray($driver);
        }

        self::assertSourceExists($driver, $source);

        $settings = $driver->getSettings($source);

        return new ConfigurationArray(array_replace_recursive($default, $settings));
    }

    /**
     * Validate configuration
     * @param ConfigurationArray $configuration
     * @param string $key
     * @param ValidatorInterface $validator
     * @param Constraint|Constraint[] $constraints
     * @throws ConfigurationNotFoundException
     */
    public static function assertMatchesSchema(
        ConfigurationArray $configuration,
        string $key,
        ValidatorInterface $validator,
        $constraints
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
     * @param DriverInterface $driver
     * @param string $source key for configuration
     * @throws ConfigurationNotFoundException
     */
    private static function assertSourceExists(DriverInterface $driver, $source)
    {
        if ($driver->keyExists($source)) {
            return;
        }

        throw new ConfigurationNotFoundException(sprintf(
            'Configuration %s not found in configuration object',
            $source
        ));
    }
}

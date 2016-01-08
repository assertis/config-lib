<?php

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
    const ENV_TEST = 'test';
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
     * @param bool $cache
     * @param Constraint|Constraint[]|null $constraints
     * @return ConfigurationArray
     */
    public function load($key = self::DEFAULT_KEY, array $default = [], $constraints = null, $cache = true)
    {
        if ($cache && isset($this->cache[$key])) {
            return $cache[$key];
        }

        $configuration = self::init($this->provider, $key, $default, $this->validator, $constraints);

        if ($cache) {
            $this->cache[$key] = $configuration;
        }

        return $configuration;
    }

    /**
     * Init configuration and return configuration object
     *
     * @param DriverInterface $provider
     * @param $key
     * @param array $default
     * @param ValidatorInterface|null $validator
     * @param Constraint|Constraint[]|null $constraints
     * @return ConfigurationArray
     * @throws Exception
     */
    public static function init(
        DriverInterface $provider,
        $key = self::DEFAULT_KEY,
        array $default = [],
        ValidatorInterface $validator = null,
        $constraints = null
    ) {
        //If configuration is lazy we can't validate structure or key
        if ($provider instanceof AbstractLazyDriver) {
            return new LazyConfigurationArray($provider);
        }

        //Validate of configuration have key
        self::validateConfiguration($provider, $key);

        //Load configuration
        $settings = $provider->getSettings($key);

        //Validate settings structure if we have validation and key is default or test
        if (!empty($validator) && !empty($constraints) && ($key === self::DEFAULT_KEY || $key === self::ENV_TEST)) {
            $violations = $validator->validate($settings, $constraints);

            if (!empty($violations)) {
                $error = "Validation errors:";

                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $error .= "\n";

                    if (!empty($violation->getPropertyPath())) {
                        $error .= "[" . $violation->getPropertyPath() . "]";
                    }

                    $error .= $violation->getMessage();
                }

                throw new Exception("Configuration $key has bad structure. $error");
            }
        }

        return new ConfigurationArray(array_merge($settings, $default));
    }

    /**
     * Validate configuration array if we have key etc.
     * @param DriverInterface $provider
     * @param string $key key for configuration
     * @throws Exception
     */
    private static function validateConfiguration(DriverInterface $provider, $key)
    {
        if (!$provider->keyExists($key)) {
            throw new Exception("Configuration $key not found in configuration object");
        }
    }
}

<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Drivers\DriverInterface;
use Assertis\Configuration\Drivers\AbstractLazyDriver;
use Exception;
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
     * @return ConfigurationArray
     */
    public function load($key = self::DEFAULT_KEY, array $default = [], $cache = true)
    {
        if ($cache && isset($this->cache[$key])) {
            return $cache[$key];
        }

        $configuration = $this->init($this->provider, $key, $default, $this->validator);

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
     * @return ConfigurationArray
     * @throws Exception
     */
    public static function init(DriverInterface $provider,
                                $key = self::DEFAULT_KEY,
                                array $default = [],
                                ValidatorInterface $validator = null)
    {
        //If configuration is lazy we can't validate structure or key
        if ($provider instanceof AbstractLazyDriver) {
            return new LazyConfigurationArray($provider);
        }

        //Validate of configuration have key
        self::validateConfiguration($provider, $key);

        //Load configuration
        $settings = $provider->getSettings($key);

        //Validate settings structure if we have validation and key is default one
        if (!empty($validator) && $key === self::DEFAULT_KEY) {
            $validator->validate($settings);
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
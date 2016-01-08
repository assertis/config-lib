<?php

namespace Assertis\Configuration;

use Exception;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Silex provider for configuration module
 *
 * @package Assertis\Configuration
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationProvider implements ServiceProviderInterface
{
    const ENV_KEY = 'ENV';
    const ENV_DEFAULT = ConfigurationFactory::DEFAULT_KEY;

    const TENANT_KEY = 'TENANT';
    const TENANT_DEFAULT = 'default';

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['config.validator'] = null;
        $app['config.validator.constraints'] = null;

        $app['config.helper'] = $app->share(function (Application $app) {
            return new ConfigurationHelper($app);
        });

        $app['config.environment'] = self::getEnv();
        $app['config.tenant'] = self::getTenant();

        $app['config.common'] = $app->share(function ($app) {
            try {
                return ConfigurationFactory::init($app['config.driver'], ConfigurationFactory::ENV_COMMON, [], null)
                    ->getSettings();
            } catch (Exception $e) {
                unset($e);
                //It will return exception if common file not exists. We don't bother of that : ).
            } finally {
                return [];
            }
        });

        $app['config.factory'] = $app->share(function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            return new ConfigurationFactory($helper->getDriver(), $helper->getValidator());
        });

        $app['config'] = $app->share(function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            /** @var ConfigurationFactory $factory */
            $factory = $app['config.factory'];

            return $factory->load($helper->getEnvironment(), $helper->getCommon(), $helper->getValidationConstraints());
        });
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    private static function getValue($name, $default = null)
    {
        $envVariable = self::getEnvironmentVariable($name);
        if (!empty($envVariable)) {
            return $envVariable;
        }

        $serverVariable = self::getServerVariable($name);
        if (!empty($serverVariable)) {
            return $serverVariable;
        }

        return $default;
    }

    /**
     * @return string
     */
    public static function getEnv()
    {
        return self::getValue(self::ENV_KEY, self::ENV_DEFAULT);
    }

    /**
     * @return string
     */
    public static function getTenant()
    {
        return self::getValue(self::TENANT_KEY, self::TENANT_DEFAULT);
    }

    /**
     * @return bool
     */
    public static function isDev()
    {
        return self::ENV_DEFAULT === self::getEnv();
    }

    /**
     * Return environment configuration
     *
     * @return string
     */
    private static function getEnvironmentVariable($name)
    {
        return getenv($name);
    }

    /**
     * Return server configuration
     *
     * @return string
     */
    private static function getServerVariable($name)
    {
        return !empty($_SERVER[$name]) ? $_SERVER[$name] : null;
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}

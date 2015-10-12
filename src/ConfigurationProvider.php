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
    const ENV_DEV = ConfigurationFactory::DEFAULT_KEY;

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['config.validator'] = null;

        $app['config.helper'] = $app->share(function (Application $app) {
            return new ConfigurationHelper($app);
        });

        $app['config.environment'] = self::getEnv();

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

        $app['config.factory'] = $app->share(function ($app){
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            return new ConfigurationFactory($helper->getDriver(), $helper->getValidator());
        });

        $app['config'] = $app->share(function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            /** @var ConfigurationFactory $factory */
            $factory = $app['config.factory'];

            return $factory->load($helper->getEnvironment(), $helper->getCommon());
        });
    }

    /**
     * @return string
     */
    private static function getEnv()
    {
        $envVariable = self::getEnvironmentVariable();
        if (!empty($envVariable)) {
            return $envVariable;
        }

        $serverVariable = self::getServerVariable();
        if (!empty($serverVariable)) {
            return $serverVariable;
        }

        return self::ENV_DEV;
    }

    /**
     * @return bool
     */
    public static function isDev()
    {
        return self::ENV_DEV === self::getEnv();
    }

    /**
     * Return environment configuration
     *
     * @return string
     */
    private static function getEnvironmentVariable()
    {
        return getenv('ENV');
    }

    /**
     * Return server configuration
     *
     * @return string
     */
    private static function getServerVariable()
    {
        return !empty($_SERVER['ENV']) ? $_SERVER['ENV'] : null;
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
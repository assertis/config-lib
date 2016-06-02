<?php

namespace Assertis\Configuration;

use Exception;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Silex provider for configuration module
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $runtime = new RuntimeSettings($_SERVER, $_GET);

        $app['config.is_dev'] = $runtime->isDev();

        if (!isset($app['config.environment'])) {
            $app['config.environment'] = $runtime->getEnv();
        }

        if (!isset($app['config.tenant'])) {
            $app['config.tenant'] = $app->share(function (Application $app) use ($runtime) {
                $tenant = $runtime->getTenant();

                if (true === $app['config.require_tenant'] && empty($tenant)) {
                    throw new Exception('Tenant header or environment setting must be provided.');
                }

                return $tenant;
            });
        }

        if (!isset($app['config.validator.constraints'])) {
            $app['config.validator.constraints'] = null;
        }

        if (!isset($app['config.validator'])) {
            $app['config.validator'] = null;
        }

        $app['config.helper'] = $app->share(function (Application $app) {
            return new ConfigurationHelper($app);
        });

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
     * @return bool
     * @deprecated Please use RuntimeSettings directly in your projects.
     */
    public static function isDev()
    {
        return (new RuntimeSettings($_SERVER, $_GET))->isDev();
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}

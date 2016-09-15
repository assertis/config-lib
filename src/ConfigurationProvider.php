<?php

namespace Assertis\Configuration;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Silex provider for configuration module
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $runtime = new RuntimeSettings($_SERVER, $_GET);

        $app['config.is_dev'] = $runtime->isDev();

        if (!isset($app['config.environment'])) {
            $app['config.environment'] = $runtime->getEnv();
        }

        if (!isset($app['config.tenant'])) {
            $app['config.tenant'] = function (Container $app) use ($runtime) {
                
                $tenant = $runtime->getTenant();
                
                if (empty($tenant)) {
                    $tenant = $app['config']->get('tenant');
                }

                if (true === $app['config.require_tenant'] && empty($tenant)) {
                    throw new Exception('Tenant header or environment setting must be provided.');
                }

                return $tenant;
            };
        }

        if (!isset($app['config.validator.constraints'])) {
            $app['config.validator.constraints'] = null;
        }

        if (!isset($app['config.validator'])) {
            $app['config.validator'] = null;
        }

        $app['config.helper'] = function (Container $app) {
            return new ConfigurationHelper($app);
        };

        $app['config.common'] = function ($app) {
            try {
                return ConfigurationFactory::init($app['config.driver'], ConfigurationFactory::ENV_COMMON, [], null)
                    ->getSettings();
            } catch (Exception $e) {
                // This means that the common file doesn't exist. Not a problem.
                return [];
            }
        };

        $app['config.factory'] = function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            return new ConfigurationFactory($helper->getDriver(), $helper->getValidator());
        };

        $app['config'] = function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            /** @var ConfigurationFactory $factory */
            $factory = $app['config.factory'];
            
            return $factory->load($helper->getEnvironment(), $helper->getCommon(), $helper->getValidationConstraints());
        };
    }
}

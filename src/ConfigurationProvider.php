<?php
declare(strict_types=1);

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
     * @return RuntimeSettings
     */
    private function getRuntimeSettings(Container $app): RuntimeSettings
    {
        if (isset($app['request_stack']) && $app['request_stack']->getCurrentRequest()) {
            $request = $app['request_stack']->getCurrentRequest();
            $serverVariables = $request->server->all();
            $urlParams = array_merge($request->query->all(), $request->request->all());
        } else {
            $serverVariables = $_SERVER;
            $urlParams = array_merge($_GET, $_POST);
        }

        return new RuntimeSettings($serverVariables, $urlParams);
    }

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['config.is_tenant_based'] = function (Container $app) {
            return !empty($app['config.tenant_based']);
        };

        $app['config.is_tenant_required'] = function (Container $app) {
            return !empty($app['config.require_tenant']) || $app['config.is_tenant_based'];
        };

        $app['config.runtime'] = function (Container $app) {
            return isset($app[RuntimeSettings::class])
                ? $app[RuntimeSettings::class]
                : $this->getRuntimeSettings($app);
        };

        if (!isset($app['config.is_dev'])) {
            $app['config.is_dev'] = function (Container $app) {
                return $app['config.runtime']->isDev();
            };
        }

        if (!isset($app['config.environment'])) {
            $app['config.environment'] = function (Container $app) {
                return $app['config.runtime']->getEnv();
            };
        }

        if (!isset($app['config.tenant'])) {
            $app['config.tenant'] = function (Container $app) {
                $tenant = $app['config.runtime']->getTenant();

                if (!$app['config.is_tenant_based'] && empty($tenant)) {
                    $tenant = $app['config']->get('tenant');
                }

                if ($app['config.is_tenant_required'] && empty($tenant)) {
                    throw new ConfigurationException('Tenant header or environment setting must be provided.');
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
                return ConfigurationFactory::init($app['config.driver'], ConfigurationFactory::ENV_COMMON, [])
                    ->getSettings();
            } catch (Exception $e) {
                // This means that the common file doesn't exist. Not a problem.
                return [];
            }
        };

        $app['config.factory'] = function ($app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            return new ConfigurationFactory(
                $helper->getDriver(),
                $helper->getValidator()
            );
        };

        $app['config.factory.tenant'] = function (Container $app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            return new TenantBasedConfigurationFactory(
                $helper->getDriver(),
                $helper->getValidator(),
                $app['config.tenant']
            );
        };

        $app['config'] = function (Container $app) {
            /** @var ConfigurationHelper $helper */
            $helper = $app['config.helper'];

            /** @var ConfigurationFactory $factory */
            $factory = $app['config.is_tenant_based'] ? $app['config.factory.tenant'] : $app['config.factory'];

            return $factory->load(
                $helper->getEnvironment(),
                $helper->getCommon(),
                $helper->getValidationConstraints()
            );
        };
    }
}

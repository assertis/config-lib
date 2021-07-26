<?php
declare(strict_types=1);

namespace Assertis\Configuration;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Silex provider for configuration module
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationProvider implements ServiceProviderInterface
{
    /**
     * @var Container
     */
    private $app;

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $this->app = $app;

        $app['config.is_tenant_based'] = function (Container $app) {
            return !empty($app['config.tenant_based']);
        };

        $app['config.is_tenant_required'] = function (Container $app) {
            return !empty($app['config.require_tenant']) || $app['config.is_tenant_based'];
        };

        $app['config.runtime'] = function (Container $app) {
            return $app[RuntimeSettings::class] ?? $this->getRuntimeSettings();
        };

        $app['config.effective_current_url'] = function (Container $app) {
            return $app['config.current_url'] ?? $app['config.runtime']->getRequestUri();
        };

        $app['config.effective_current_path'] = function (Container $app) {
            return $app['config.current_url'] ?? $app['config.runtime']->getPathInfo();
        };

        $app['config.tenant.default'] = function (Container $app) {
            return
                $app['config.default_tenant_provider']
                ?? TenantBasedConfigurationFactory::getDefaultTenant(
                    $app['config.driver'],
                    $app['config.environment']
                );
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

                if (!empty($app['config.use_default_tenant'])) {
                    return $app['config.tenant.default'];
                }

                $tenant = $app['config.runtime']->getTenant();

                if (!$app['config.is_tenant_based'] && empty($tenant) && !$app['config.is_tenant_required']) {
                    $tenant = $app['config']->get('tenant');
                }

                if (empty($tenant) && $app['config.is_tenant_required']) {
                    if ($this->isRequiredTenantException()) {
                        $tenant = $app['config.tenant.default'];
                    } else {
                        throw new ConfigurationException('Tenant header or environment setting must be provided.');
                    }
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
            } catch (Exception $ex) {
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
            /** @var string|null $tenant */
            $tenant = $app['config.tenant'];

            if (!$tenant) {
                throw new RuntimeException('Tenant required');
            }

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

    private function isRequiredTenantException(): bool
    {
        if (empty($this->app['config.exceptions'])) {
            return false;
        }

        return in_array($this->app['config.effective_current_path'], $this->app['config.exceptions'], true);
    }

    /**
     * @param Container $app
     * @return RuntimeSettings
     */
    private function getRuntimeSettings(): RuntimeSettings
    {
        if (isset($this->app['request_stack']) && $this->app['request_stack']->getCurrentRequest()) {
            /** @var Request $request */
            $request = $this->app['request_stack']->getCurrentRequest();
            $serverVariables = $request->server->all();
            $urlParams = array_merge($request->query->all(), $request->request->all());
            $extra = $this->getExtraValuesFromRequestHeaders($request);
        } else {
            $serverVariables = $_SERVER;
            $urlParams = array_merge($_GET, $_POST);
            $extra = [];
        }

        return new RuntimeSettings($serverVariables, $urlParams, $extra);
    }

    private function getExtraValuesFromRequestHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        $extra = [];

        array_walk($headers, function (array $value, string $key) use (&$extra) {
            if (stripos($key, 'x-') !== 0) {
                return;
            }
            /** @var string $newKey */
            $newKey = substr($key, 2);
            $extra[$newKey] = $value[0];
        });

        return $extra;
    }
}

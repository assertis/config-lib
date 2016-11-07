<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\DriverInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This extension allows for additional distinction between configuration values for different tenants within
 * the same environment. It also allows for having a common set of values for all tenants within the environment.
 *
 * This:
 *
 * {
 *   "@all" {
 *     "foo": {
 *       "bar": "Baz"
 *     }
 *   },
 *   "Tenant": {
 *     "foo": {
 *       "boo": "Bing"
 *     },
 *     "fab": 42
 *   }
 * }
 *
 * For tenant "Tenant" results in:
 *
 * {
 *   "foo": {
 *     "bar": "Baz",
 *     "boo": "Bing",
 *   },
 *   "fab": 42
 * }
 *
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TenantBasedConfigurationFactory extends ConfigurationFactory
{
    const ALL_TENANT_KEY = '@all';

    /**
     * @var string
     */
    private $tenant;

    /**
     * @param DriverInterface $provider
     * @param null|ValidatorInterface $validator
     * @param string $tenant
     */
    public function __construct(
        DriverInterface $provider,
        ValidatorInterface $validator = null,
        string $tenant
    ) {
        parent::__construct($provider, $validator);
        $this->tenant = $tenant;
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
        $config = parent::doLoad($provider, $key, $default);

        /** @var ConfigurationArray $all */
        $all = $config->get(self::ALL_TENANT_KEY);
        /** @var ConfigurationArray $tenantSpecific */
        $tenantSpecific = $config->get($this->tenant);

        return new ConfigurationArray(array_merge_recursive(
            $all->getSettings(),
            $tenantSpecific->getSettings()
        ));
    }
}

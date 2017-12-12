<?php
declare(strict_types = 1);

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\DriverInterface;
use Exception;
use RuntimeException;
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
     * @param DriverInterface $driver
     * @param null|ValidatorInterface $validator
     * @param string $tenant
     */
    public function __construct(
        DriverInterface $driver,
        ValidatorInterface $validator = null,
        string $tenant
    ) {
        parent::__construct($driver, $validator);
        $this->tenant = $tenant;
    }

    /**
     * @param DriverInterface $driver
     * @param string $source
     * @param array $default
     * @return ConfigurationArray|LazyConfigurationArray
     * @throws ConfigurationNotFoundException
     */
    protected function doLoad(
        DriverInterface $driver,
        string $source = self::DEFAULT_KEY,
        array $default = []
    ): ConfigurationArray {
        $config = parent::doLoad($driver, $source, $default);

        /** @var ConfigurationArray $all */
        $all = $config->get(self::ALL_TENANT_KEY);

        if (!$config->offsetExists($this->tenant)) {
            throw new ConfigurationNotFoundException(sprintf(
                'Configuration for tenant %s does not exist in environment %s',
                $this->tenant,
                $source
            ));
        }

        /** @var ConfigurationArray $tenantSpecific */
        $tenantSpecific = $config->get($this->tenant);

        return new ConfigurationArray(array_replace_recursive(
            $all->getSettings(),
            $tenantSpecific->getSettings()
        ));
    }

    /**
     * List all tenants in selected environment.
     *
     * @param DriverInterface $driver
     * @param string $source
     * @return array
     * @throws Exception
     */
    public static function getTenants(DriverInterface $driver, string $source): array
    {
        $config = parent::init($driver, $source);
        $ignored = [self::ALL_TENANT_KEY, self::COMMENT_KEY];
        
        $keys = array_keys($config->getAll()->getArrayCopy());
        
        return array_values(array_diff($keys, $ignored));
    }

    /**
     * @param DriverInterface $driver
     * @param string $source
     * @return string
     * @throws Exception
     */
    public static function getDefaultTenant(DriverInterface $driver, string $source): string
    {
        $tenants = self::getTenants($driver, $source);
        
        if (empty($tenants)) {
            throw new RuntimeException('No tenants found');
        }
        
        return $tenants[0];
    }
}

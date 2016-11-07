<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\DriverInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class for creating configurations object
 *
 * @package Assertis\Configuration
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class TenantBasedConfigurationFactory extends ConfigurationFactory
{
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
        $all = $config->get('@all');
        /** @var ConfigurationArray $tenantSpecific */
        $tenantSpecific = $config->get($this->tenant);

        return new ConfigurationArray(array_merge_recursive(
            $all->getAll()->getArrayCopy(),
            $tenantSpecific->getAll()->getArrayCopy()
        ));
    }
}

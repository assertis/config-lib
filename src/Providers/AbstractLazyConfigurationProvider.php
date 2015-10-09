<?php

namespace Assertis\Configuration\Providers;

use Assertis\Configuration\ConfigurationArray;
use Exception;

/**
 * Interface for lazy loading of configuration. For example for database stored configurations.
 *
 * @package Assertis\Configuration\Providers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractLazyConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * Return searched configuration
     *
     * @param $key
     * @return mixed|ConfigurationArray
     */
    abstract public function get($key);

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getSettings($key)
    {
        throw new Exception("Lazy provider can't return all settings.");
    }

    /**
     * @inheritdoc
     */
    public function keyExists($key)
    {
        $value = $this->get($key);

        return !empty($value);
    }


}
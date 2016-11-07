<?php

namespace Assertis\Configuration\Drivers;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\ConfigurationException;
use Exception;

/**
 * Interface for lazy loading of configuration. For example for database stored configurations.
 *
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractLazyDriver implements DriverInterface
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
        throw new ConfigurationException("Lazy provider can't return all settings.");
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

<?php

namespace Assertis\Configuration\Collection;

use Assertis\Configuration\ConfigurationException;
use Assertis\Configuration\Drivers\AbstractLazyDriver;

/**
 * Configuration array for lazy configs
 *
 * @package Assertis\Configuration\Collection
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class LazyConfigurationArray extends ConfigurationArray
{
    /**
     * @var AbstractLazyDriver
     */
    private $provider;

    /**
     * LazyConfigurationArray constructor.
     * @param AbstractLazyDriver $provider
     */
    public function __construct(AbstractLazyDriver $provider)
    {
        parent::__construct();
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function set($key, $value)
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function getAll()
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function setAll(array $settings)
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function getIterator()
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function offsetExists($offset)
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->getWithType($this->provider->get($offset));
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function offsetSet($offset, $value)
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function offsetUnset($offset)
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function count()
    {
        throw new ConfigurationException("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws ConfigurationException
     */
    public function getSettings()
    {
        throw new ConfigurationException("Method not allowed");
    }
}

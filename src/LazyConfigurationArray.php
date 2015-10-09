<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Providers\AbstractLazyConfigurationProvider;
use Exception;

/**
 * Configuration array for lazy configs
 *
 * @package Assertis\Configuration
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class LazyConfigurationArray extends ConfigurationArray
{
    /**
     * @var AbstractLazyConfigurationProvider
     */
    private $provider;

    /**
     * LazyConfigurationArray constructor.
     * @param AbstractLazyConfigurationProvider $provider
     */
    public function __construct(AbstractLazyConfigurationProvider $provider)
    {
        $this->provider = $provider;
    }

    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function set($key, $value)
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAll()
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function setAll(array $settings)
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getIterator()
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetExists($offset)
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetGet($offset)
    {
        return $this->getWithType($this->provider->get($offset));
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception("Method not allowed");
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function count()
    {
        throw new Exception("Method not allowed");
    }
}
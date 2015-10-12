<?php

namespace Assertis\Configuration\Collection;

use Assertis\Configuration\Drivers\AbstractLazyDriver;
use Exception;

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

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getSettings()
    {
        throw new Exception("Method not allowed");
    }


}
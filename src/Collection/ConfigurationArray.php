<?php

namespace Assertis\Configuration\Collection;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class ConfigurationArray
 * @package Assertis\Configuration\Collection
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 *
 * Class provide all method needed for configuration
 */
class ConfigurationArray implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * List of settings
     * @var array
     */
    private $settings = [];

    /**
     * Set default configuration to object
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
    }

    /**
     * Return raw settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Alias method for offset get
     *
     * @param mixed $key Value key
     * @return self|mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Return $default if value is not empty
     * @param $key
     * @param $default
     * @return mixed
     */
    public function getDefault($key, $default)
    {
        if (isset($this->settings[$key])) {
            return $this->get($key);
        } else {
            return $default;
        }
    }

    /**
     * Alias method for offset set
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Alias method for offset unset
     *
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
        return $this;
    }

    /**
     * Alias for get iterator
     *
     * @return ArrayIterator
     */
    public function getAll()
    {
        return $this->getIterator();
    }

    /**
     * Set configuration data
     *
     * @param array $settings
     * @return $this
     */
    public function setAll(array $settings)
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->settings);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->settings[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->settings)) {
            return $this->getWithType($this->settings[$offset]);
        }

        if (is_string($offset) && strpos($offset, '.') !== -1) {
            return $this->getNestedVar($this->settings, $offset);
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->settings[] = $value;
        } else {
            $this->settings[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (is_numeric($offset)) {
            array_splice($this->settings, $offset, 1);
        } else {
            unset($this->settings[$offset]);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->settings);
    }

    /**
     * Return nested value
     *
     * @param $context
     * @param $name
     * @url http://stackoverflow.com/questions/2286706/php-lookup-array-contents-with-dot-syntax
     * @return mixed|ConfigurationArray
     */
    protected function getNestedVar(&$context, $name)
    {
        $pieces = explode('.', $name);
        foreach ($pieces as $piece) {
            if (!is_array($context) || !array_key_exists($piece, $context)) {
                // error occurred
                return null;
            }
            $context = &$context[$piece];
        }
        return $this->getWithType($context);
    }

    /**
     * Secure array to have same functionality like configuration arrays
     *
     * @param $value
     * @return mixed|ConfigurationArray
     */
    protected function getWithType($value)
    {
        if (is_array($value)) {
            return new ConfigurationArray($value);
        }
        return $value;
    }
}

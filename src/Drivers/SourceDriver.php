<?php

namespace Assertis\Configuration\Drivers;

/**
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class SourceDriver extends AbstractClassDriver
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Return array with settings
     *
     * @param string $key key for loading key
     * @return array
     */
    public function getSettings($key)
    {
        return $this->settings[$key];
    }

    /**
     * @inheritdoc
     */
    public function keyExists($key)
    {
        return array_key_exists($key, $this->settings);
    }
}

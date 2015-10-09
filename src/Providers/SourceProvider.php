<?php

namespace Assertis\Configuration\Providers;

/**
 * @package Assertis\Configuration\Providers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class SourceProvider extends AbstractClassProvider
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
}
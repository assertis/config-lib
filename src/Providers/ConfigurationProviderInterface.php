<?php

namespace Assertis\Configuration\Providers;

/**
 * Interface for providing configuration data
 *
 * @package Assertis\Configuration\Providers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
interface ConfigurationProviderInterface
{
    /**
     * Return array with settings
     *
     * @param string $key key for loading key
     * @return array
     */
    public function getSettings($key);

    /**
     * @param $key
     * @return mixed
     */
    public function keyExists($key);
}
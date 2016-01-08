<?php

namespace Assertis\Configuration\Drivers;

/**
 * Interface for providing configuration data
 *
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
interface DriverInterface
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

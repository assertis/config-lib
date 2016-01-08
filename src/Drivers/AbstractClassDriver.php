<?php

namespace Assertis\Configuration\Drivers;

/**
 * Provider for configuration in class, array based
 *
 * @package Assertis\Configuration\Drivers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractClassDriver implements DriverInterface
{
    /**
     * @inheritdoc
     */
    public function keyExists($key)
    {
        $value = $this->getSettings($key);

        return !empty($value);
    }
}

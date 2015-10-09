<?php

namespace Assertis\Configuration\Providers;

/**
 * Provider for configuration in class, array based
 *
 * @package Assertis\Configuration\Providers
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
abstract class AbstractClassProvider implements ConfigurationProviderInterface
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
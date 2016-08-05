<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\DriverInterface;
use Pimple\Container;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class with configuration module interfaces
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationHelper
{
    /**
     * @var Container
     */
    private $app;

    /**
     * ConfigurationHelper constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return ConfigurationArray|LazyConfigurationArray
     */
    public function getConfig()
    {
        return $this->app['config'];
    }

    /**
     * @return array
     */
    public function getCommon()
    {
        return $this->app['config.common'];
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->app['config.driver'];
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->app['config.environment'];
    }

    /**
     * @return string
     */
    public function getTenant()
    {
        return $this->app['config.tenant'];
    }

    /**
     * @return null|ValidatorInterface
     */
    public function getValidator()
    {
        return $this->app['config.validator'];
    }

    /**
     * @return null|Constraint|Constraint[]
     */
    public function getValidationConstraints()
    {
        return $this->app['config.validator.constraints'];
    }
}

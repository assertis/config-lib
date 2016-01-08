<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Collection\LazyConfigurationArray;
use Assertis\Configuration\Drivers\DriverInterface;
use Silex\Application;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class with configuration module interfaces
 *
 * @package Assertis\Configuration
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationHelper
{
    /**
     * @var Application
     */
    private $app;

    /**
     * ConfigurationHelper constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
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

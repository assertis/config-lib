<?php

namespace Assertis\Configuration;


use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Drivers\SourceDriver;
use Silex\Application;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    public function setUp()
    {
        $this->app = new Application();
        $this->app->register(new ValidatorServiceProvider());
    }

    public function testInit()
    {
        $settings = ConfigurationFactory::init(new SourceDriver(['dev' => ['something' => 'asd']]));
        $this->assertTrue($settings instanceof ConfigurationArray);
    }

    public function testByConstruct()
    {
        $factory = new ConfigurationFactory(new SourceDriver(['dev' => ['something' => 'asd']]));
        $this->assertTrue($factory->load() instanceof ConfigurationArray);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidator()
    {
        $factory = new ConfigurationFactory(new SourceDriver(['dev' => ['something' => 'asd']]), $this->app['validator']);
        $constraints = [
            new Assert\Count(['min' => 2])
        ];
        $factory->load(ConfigurationFactory::DEFAULT_KEY, [], $constraints, true);
    }

    /**
     * @expectedException \Exception
     */
    public function testBadStructure()
    {
        $factory = new ConfigurationFactory(new SourceDriver([]));
        $factory->load();
    }
}

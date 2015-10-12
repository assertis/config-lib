<?php

namespace Assertis\Configuration;


use Assertis\Configuration\Drivers\SourceDriver;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
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
    public function testBadStructure()
    {
        $factory = new ConfigurationFactory(new SourceDriver([]));
        $factory->load();
    }
}

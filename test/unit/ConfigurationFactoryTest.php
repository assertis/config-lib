<?php

namespace Assertis\Configuration;


use Assertis\Configuration\Providers\SourceProvider;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $settings = ConfigurationFactory::init(new SourceProvider(['dev' => ['something' => 'asd']]));
        $this->assertTrue($settings instanceof ConfigurationArray);
    }

    public function testByConstruct()
    {
        $factory = new ConfigurationFactory(new SourceProvider(['dev' => ['something' => 'asd']]));
        $this->assertTrue($factory->load() instanceof ConfigurationArray);
    }

    /**
     * @expectedException \Exception
     */
    public function testBadStructure()
    {
        $factory = new ConfigurationFactory(new SourceProvider([]));
        $factory->load();
    }
}

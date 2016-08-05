<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Drivers\File\IniDriver;
use PHPUnit_Framework_TestCase;
use Pimple\Container;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        $_SERVER['ENV'] = 'test';
        $path = ROOT . '/test/resources/';
        $provider = new ConfigurationProvider();
        
        $this->container = new Container();
        $this->container['config.driver'] = new IniDriver($path);
        $this->container->register($provider);
    }

    public function testGetters()
    {
        $helper = new ConfigurationHelper($this->container);

        $this->assertInstanceOf(ConfigurationArray::class, $helper->getConfig());
        $this->assertEmpty($helper->getCommon());
        $this->assertInstanceOf(IniDriver::class, $helper->getDriver());
        $this->assertEquals('test', $helper->getEnvironment());
        $this->assertNull($helper->getValidator());
    }
}

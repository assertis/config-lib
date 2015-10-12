<?php

namespace Assertis\Configuration;


use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Drivers\File\IniDriver;
use Silex\Application;

class ConfigurationHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    public function setUp()
    {
        $_SERVER['ENV'] = 'test';
        $path = ROOT . '/test/resources/';
        $this->app = new Application();
        $this->app['config.driver'] = new IniDriver($path);
        $provider = new ConfigurationProvider();
        $this->app->register($provider);
    }

    public function testGetters()
    {
        $helper = new ConfigurationHelper($this->app);

        $this->assertInstanceOf(ConfigurationArray::class, $helper->getConfig());
        $this->assertEmpty($helper->getCommon());
        $this->assertInstanceOf(IniDriver::class, $helper->getDriver());
        $this->assertEquals('test', $helper->getEnvironment());
        $this->assertNull($helper->getValidator());
    }
}

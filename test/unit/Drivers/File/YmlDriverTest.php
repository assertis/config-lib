<?php

namespace Assertis\Configuration\Drivers\File;


class YmlDriverTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp()
    {
        $this->path = ROOT . '/test/resources/';
    }

    public function testLoadingFile()
    {
        $provider = new YmlDriver($this->path);
        $settings = $provider->getSettings('test');
        $this->assertNotEmpty($settings);
    }
}

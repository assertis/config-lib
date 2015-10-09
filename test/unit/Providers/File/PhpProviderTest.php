<?php

namespace Assertis\Configuration\Providers\File;


class PhpProviderTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp()
    {
        $this->path = ROOT . '/test/resources/';
    }

    public function testLoadingFile()
    {
        $provider = new PhpProvider($this->path);
        $settings = $provider->getSettings('test');
        $this->assertNotEmpty($settings);
    }
}

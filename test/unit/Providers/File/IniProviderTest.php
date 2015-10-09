<?php

namespace Assertis\Configuration\Providers\File;


class IniProviderTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp() {
        $this->path = ROOT . '/test/resources/';
    }

    public function testLoadingFile(){
        $provider = new IniProvider($this->path);
        $settings = $provider->getSettings('test');
        $this->assertNotEmpty($settings);
    }
}

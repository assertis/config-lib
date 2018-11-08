<?php

namespace Assertis\Configuration\Drivers\File;



/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class JsonDriverTest extends \PHPUnit_Framework_TestCase
{
    private $path;

    protected function setUp()
    {
        $this->path = ROOT . '/test/resources/';
    }

    public function testLoadingFile()
    {
        $provider = new JsonDriver($this->path);
        $settings = $provider->getSettings('test');

        $this->assertInternalType('array', $settings);
        $this->assertSame('json', $settings['testKey']);
    }

    /**
     * @expectedException \Assertis\Configuration\ConfigurationJsonException
     * (custom exceptions need FQCN here)
     */
    public function testValidate()
    {
        $provider = new JsonDriver($this->path);
        $provider->validate('test-incorrect');
    }
}

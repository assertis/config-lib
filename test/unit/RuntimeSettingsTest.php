<?php
declare(strict_types=1);

namespace Assertis\Configuration;

use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class RuntimeSettingsTest extends PHPUnit_Framework_TestCase
{
    public function testReturnDefaultOrNullIfMissing()
    {
        $default = new stdClass();

        $settings = new RuntimeSettings([], []);

        $this->assertSame($default, $settings->getValue('foo', $default));
        $this->assertNull($settings->getValue('Foo'));
    }

    public function testGetFromExtra()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings([], [], ['Foo' => $value]);

        $this->assertSame($value, $settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertSame($value, $settings->getValue('FOO'));
    }

    public function testGetFromHeader()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings(['HTTP_X_FOO' => $value], []);

        $this->assertSame($value, $settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertSame($value, $settings->getValue('FOO'));
    }

    public function testGetFromEnvironment()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings(['Foo' => $value], []);

        $this->assertNull($settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertNull($settings->getValue('FOO'));
    }

    public function testGetFromUrl()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings([], ['foo' => $value]);

        $this->assertSame($value, $settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertSame($value, $settings->getValue('FOO'));
    }

    /**
     * @return array
     */
    public function providePrecedence(): array
    {
        return [
            [
                ['HTTP_X_FOO' => 'header', 'Foo' => 'environment'],
                ['foo' => 'get'],
                ['Foo' => 'extra'],
                'extra',
            ],
            [
                ['HTTP_X_FOO' => 'header', 'Foo' => 'environment'],
                ['foo' => 'get'],
                [],
                'get',
            ],
            [
                ['HTTP_X_FOO' => 'header', 'Foo' => 'environment'],
                [],
                [],
                'header',
            ],
            [
                ['Foo' => 'environment'],
                [],
                [],
                'environment',
            ],
        ];
    }

    /**
     * @dataProvider providePrecedence
     * @param array $server
     * @param array $get
     * @param array $extra
     * @param string $expected
     */
    public function testPrecedence(array $server, array $get, array $extra, string $expected)
    {
        $settings = new RuntimeSettings($server, $get, $extra);

        $this->assertSame($expected, $settings->getValue('Foo'));
    }
}

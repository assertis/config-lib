<?php

namespace Assertis\Configuration;

use PHPUnit_Framework_TestCase;
use Silex\Application;
use stdClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class RuntimeSettingsTest extends PHPUnit_Framework_TestCase
{
    public function testReturnDefaultOrNullIfMissing()
    {
        $default = new stdClass();

        $settings = new RuntimeSettings([]);

        $this->assertSame($default, $settings->getValue('foo', $default));
        $this->assertSame(null, $settings->getValue('Foo'));
    }

    public function testGetFromHeader()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings([
            'HTTP_X_FOO' => $value,
        ]);

        $this->assertSame($value, $settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertSame($value, $settings->getValue('FOO'));
    }

    public function testGetFromEnvironment()
    {
        $value = new stdClass();

        $settings = new RuntimeSettings([
            'Foo' => $value,
        ]);

        $this->assertSame(null, $settings->getValue('foo'));
        $this->assertSame($value, $settings->getValue('Foo'));
        $this->assertSame(null, $settings->getValue('FOO'));
    }

    public function testHeaderHasPrecedence()
    {
        $settings = new RuntimeSettings([
            'HTTP_X_FOO' => 'header',
            'Foo'        => 'environment',
        ]);

        $this->assertSame('header', $settings->getValue('Foo'));
    }
}

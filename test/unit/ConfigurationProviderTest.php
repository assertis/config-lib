<?php
declare(strict_types=1);

namespace Assertis\Configuration;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Pimple\Container;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class ConfigurationProviderTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $container = new Container();

        $provider = new ConfigurationProvider();
        $provider->register($container);

        static::assertSame('dev', $container['config.environment']);
    }

    public function testRequestStack()
    {
        $container = new Container();

        /** @var Request|PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);
        $request->server = new ParameterBag(['ENV' => 'foo']);
        $request->query = new ParameterBag(['tenant' => 'bar']);
        $request->request = new ParameterBag([]);

        $stack = $this->createMock(RequestStack::class);
        $stack->method('getCurrentRequest')->willReturn($request);

        $container['request_stack'] = $stack;

        $provider = new ConfigurationProvider();
        $provider->register($container);

        static::assertSame('foo', $container['config.environment']);
        static::assertSame('bar', $container['config.tenant']);
    }
}

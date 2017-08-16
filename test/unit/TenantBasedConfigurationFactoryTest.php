<?php
declare(strict_types=1);

namespace Assertis\Configuration;

use Assertis\Configuration\Drivers\DriverInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class TenantBasedConfigurationFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DriverInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $driver;
    /**
     * @var ValidatorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $validator;
    /**
     * @var string
     */
    private $env;
    /**
     * @var string
     */
    private $tenant;
    /**
     * @var TenantBasedConfigurationFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();

        $this->driver = $this->createMock(DriverInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->env = 'env';
        $this->tenant = 'tenant';

        $this->factory = new TenantBasedConfigurationFactory($this->driver, $this->validator, $this->tenant);
    }

    public function testMergingConfigs()
    {
        $common = [
            '@all' => [
                'from' => 'common',
                'num'  => 1,
                'hash' => [
                    'common'           => 'common',
                    'overwrite-env'    => 'common',
                    'overwrite-tenant' => 'common',
                ],
                'list' => ['common']
            ]
        ];

        $env = [
            '@all'   => [
                'from' => 'env',
                'num'  => 2,
                'hash' => [
                    'env'           => 'env',
                    'overwrite-env' => 'env',
                ],
                'list' => ['env']
            ],
            'tenant' => [
                'from' => 'tenant',
                'num'  => 3,
                'hash' => [
                    'tenant'           => 'tenant',
                    'overwrite-tenant' => 'tenant',
                ],
                'list' => ['tenant']
            ]
        ];

        $expected = [
            'from' => 'tenant',
            'num'  => 3,
            'hash' => [
                'common'           => 'common',
                'overwrite-env'    => 'env',
                'overwrite-tenant' => 'tenant',
                'env'              => 'env',
                'tenant'           => 'tenant',
            ],
            'list' => ['tenant']
        ];

        $this->driver->method('keyExists')->with('env')->willReturn(true);
        $this->driver->method('getSettings')->with('env')->willReturn($env);

        static::assertSame($expected, $this->factory->load('env', $common)->getAll()->getArrayCopy());
    }
}

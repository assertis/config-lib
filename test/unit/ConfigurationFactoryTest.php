<?php

namespace Assertis\Configuration;

use Assertis\Configuration\Collection\ConfigurationArray;
use Assertis\Configuration\Drivers\File\JsonDriver;
use Assertis\Configuration\Drivers\SourceDriver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return ValidatorInterface
     */
    private function getValidator()
    {
        $classMetadataFactory = new LazyLoadingMetadataFactory(new StaticMethodLoader());
        $validatorFactory = new ConstraintValidatorFactory();

        $builder = Validation::createValidatorBuilder();
        $builder->setConstraintValidatorFactory($validatorFactory);
        $builder->setTranslationDomain('validators');
        $builder->setMetadataFactory($classMetadataFactory);

        return $builder->getValidator();
    }

    public function testInit()
    {
        $settings = ConfigurationFactory::init(new SourceDriver(['dev' => ['something' => 'asd']]));
        $this->assertTrue($settings instanceof ConfigurationArray);
    }

    public function testByConstruct()
    {
        $factory = new ConfigurationFactory(new SourceDriver(['dev' => ['something' => 'asd']]));
        $this->assertTrue($factory->load() instanceof ConfigurationArray);
    }

    /**
     * @expectedException \Exception
     */
    public function testValidator()
    {
        $factory = new ConfigurationFactory(
            new SourceDriver(['dev' => ['something' => 'asd']]),
            $this->getValidator()
        );

        $constraints = [
            new Assert\Count(['min' => 2]),
        ];

        $factory->load(ConfigurationFactory::DEFAULT_KEY, [], $constraints, true);
    }

    /**
     * @expectedException \Exception
     */
    public function testBadStructure()
    {
        $factory = new ConfigurationFactory(new SourceDriver([]));
        $factory->load();
    }

    /**
     * @expectedException \Assertis\Configuration\ConfigurationJsonException
     * (custom exceptions need FQCN here)
     */
    public function testSourceValidation()
    {
        $driver = new JsonDriver(ROOT . 'test/resources/');
        ConfigurationFactory::init($driver, 'test-incorrect');
    }
}

<?php

namespace Assertis\Configuration;


class ConfigurationArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationArray
     */
    private $array;

    protected function setUp()
    {
        $this->array = new ConfigurationArray(["testKey" => "testValue"]);
    }

    public function testGet()
    {
        $this->assertEquals($this->array->get("testKey"), "testValue");
        $this->assertNull($this->array->get("xxxx"));
    }

    public function testGetDefault()
    {
        $this->assertEquals($this->array->getDefault("testKey", "someDefaultValue"), "testValue");
        $this->assertEquals($this->array->getDefault("xxx", "someDefaultValue"), "someDefaultValue");
    }

    public function testSet()
    {
        $this->array->set("secondKey", "secondValue");
        $this->assertEquals($this->array->get("secondKey"), "secondValue");
        $this->assertNotNull($this->array->get("testKey"));
    }

    public function testSetAll()
    {
        $this->array->setAll(["secondKey" => "secondValue"]);
        $this->assertEquals($this->array->get("secondKey"), "secondValue");
        $this->assertNull($this->array->get("testKey"));
    }

    public function testCount()
    {
        $this->assertEquals($this->array->count(), 1);
    }

    public function testRemove()
    {
        $this->assertEquals($this->array->count(), 1);
        $this->assertEquals($this->array->remove("testKey")->count(), 0);
    }

    public function testSearchByKey()
    {
        $this->array->setAll(["test" => ["test" => ["otherKey" => "value"]]]);
        $this->assertEquals($this->array->get("test.test.otherKey"), "value");
    }

    public function testReturningType()
    {
        $this->array->setAll(["test" => ["key" => "value", "test" => ["key" => "value"]]]);
        $this->assertTrue($this->array->get("test") instanceof ConfigurationArray);
        $this->assertTrue($this->array->get("test.test") instanceof ConfigurationArray);
    }
}

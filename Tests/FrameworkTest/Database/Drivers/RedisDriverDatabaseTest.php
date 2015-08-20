<?php

namespace FrameworkTest\Database\Drivers;

use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Database\Table;
use Tests\FrameworkTest\BaseTest;

class RedisDriverDatabaseTest extends BaseTest
{
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new RedisDatabaseDriver([
            'database' => 1
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->driver->dropAll();
    }

    public function testInsert()
    {
        // Arrange
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');

        // Act
        $this->driver->insert($t, [
            'int1' => 1,
            'str1' => 'a string'
        ]);

        // Assert
        $this->assertTrue(true);
    }

    public function testInsertWithIndex()
    {
        // Arrange
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1')->addIndex();
        $t->string('str1')->addIndex();

        // Act
        $this->driver->insert($t, [
            'int1' => 1,
            'str1' => 'a string'
        ]);

        // Assert
        $this->assertTrue(true);
    }

    public function testSelect()
    {
        // Arrange

        // Act

        // Assert
        $this->assertTrue(false);
    }
}
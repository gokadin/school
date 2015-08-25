<?php

namespace FrameworkTest\Database\Drivers;

use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Database\Table;
use Predis\Client;
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

    public function testCreateCreatesSetWithColumnNames()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');

        // Act
        $this->driver->create($t);

        // Assert
        $columnsKey = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':columns';
        $this->assertEquals(1, $redis->exists($columnsKey));
        $columnNames = $redis->smembers($columnsKey);
        $this->assertTrue(in_array('id', $columnNames));
        $this->assertTrue(in_array('int1', $columnNames));
        $this->assertTrue(in_array('str1', $columnNames));
    }

    public function testCreateCreatesHashForEachColumn()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(1, $redis->exists($columnKeyPrefix.'id'));
        $this->assertEquals(1, $redis->exists($columnKeyPrefix.'int1'));
        $this->assertEquals(1, $redis->exists($columnKeyPrefix.'str1'));
    }

    public function testCreateCreatesColumnWhenNullable()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->nullable();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(1, $redis->hget($columnKeyPrefix.'int1', 'isNullable'));
        $this->assertEquals(0, $redis->hget($columnKeyPrefix.'int2', 'isNullable'));
    }

    public function testCreateCreatesColumnWhenItHasIndex()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->addIndex();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(1, $redis->hget($columnKeyPrefix.'int1', 'hasIndex'));
        $this->assertEquals(0, $redis->hget($columnKeyPrefix.'int2', 'hasIndex'));
    }

    public function testCreateCreatesColumnWhenItHasDefault()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->default(3);
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(1, $redis->hget($columnKeyPrefix.'int1', 'isDefault'));
        $this->assertEquals(3, $redis->hget($columnKeyPrefix.'int1', 'defaultValue'));
        $this->assertEquals(0, $redis->hget($columnKeyPrefix.'int2', 'isDefault'));
    }

    public function testCreateCreatesColumnWhenUnique()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->unique();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(1, $redis->hget($columnKeyPrefix.'int1', 'isUnique'));
        $this->assertEquals(0, $redis->hget($columnKeyPrefix.'int2', 'isUnique'));
    }

    public function testCreateCreatesColumnWhenRequired()
    {
        // Arrange
        $redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->nullable();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA_PREFIX.':'.$t->name().':column:';
        $this->assertEquals(0, $redis->hget($columnKeyPrefix.'int1', 'isRequired'));
        $this->assertEquals(1, $redis->hget($columnKeyPrefix.'int2', 'isRequired'));
    }
}
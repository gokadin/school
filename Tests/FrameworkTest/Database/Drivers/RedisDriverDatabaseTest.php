<?php

namespace FrameworkTest\Database\Drivers;

use Library\Database\Drivers\RedisDatabaseDriver;
use Library\Database\Table;
use Predis\Client;
use Tests\FrameworkTest\BaseTest;

class RedisDriverDatabaseTest extends BaseTest
{
    protected $driver;
    protected $redis;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new RedisDatabaseDriver([
            'database' => 1
        ]);

        $this->redis = new Client(['database' => 1]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->driver->dropAll();
    }

    public function testCreateCreatesSetWithColumnNames()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');

        // Act
        $this->driver->create($t);

        // Assert
        $columnsKey = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMNS;
        $this->assertEquals(1, $this->redis->exists($columnsKey));
        $columnNames = $this->redis->smembers($columnsKey);
        $this->assertTrue(in_array('id', $columnNames));
        $this->assertTrue(in_array('int1', $columnNames));
        $this->assertTrue(in_array('str1', $columnNames));
    }

    public function testCreateCreatesHashForEachColumn()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(1, $this->redis->exists($columnKeyPrefix.'id'));
        $this->assertEquals(1, $this->redis->exists($columnKeyPrefix.'int1'));
        $this->assertEquals(1, $this->redis->exists($columnKeyPrefix.'str1'));
    }

    public function testCreateCreatesColumnWhenNullable()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->nullable();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(1, $this->redis->hget($columnKeyPrefix.'int1', 'isNullable'));
        $this->assertEquals(0, $this->redis->hget($columnKeyPrefix.'int2', 'isNullable'));
    }

    public function testCreateCreatesColumnWhenItHasIndex()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->addIndex();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(1, $this->redis->hget($columnKeyPrefix.'int1', 'hasIndex'));
        $this->assertEquals(0, $this->redis->hget($columnKeyPrefix.'int2', 'hasIndex'));
    }

    public function testCreateCreatesColumnWhenItHasDefault()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->default(3);
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(1, $this->redis->hget($columnKeyPrefix.'int1', 'isDefault'));
        $this->assertEquals(3, $this->redis->hget($columnKeyPrefix.'int1', 'defaultValue'));
        $this->assertEquals(0, $this->redis->hget($columnKeyPrefix.'int2', 'isDefault'));
    }

    public function testCreateCreatesColumnWhenUnique()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->unique();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(1, $this->redis->hget($columnKeyPrefix.'int1', 'isUnique'));
        $this->assertEquals(0, $this->redis->hget($columnKeyPrefix.'int2', 'isUnique'));
    }

    public function testCreateCreatesColumnWhenRequired()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->integer('int1')->nullable();
        $t->integer('int2');

        // Act
        $this->driver->create($t);

        // Assert
        $columnKeyPrefix = RedisDatabaseDriver::SCHEMA.':'.$t->name().':'.RedisDatabaseDriver::COLUMN.':';
        $this->assertEquals(0, $this->redis->hget($columnKeyPrefix.'int1', 'isRequired'));
        $this->assertEquals(1, $this->redis->hget($columnKeyPrefix.'int2', 'isRequired'));
    }

    public function testInsert()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $t->string('str1');
        $this->driver->create($t);

        // Act
        $id = $this->driver->table($t->name())->insert(['int1' => 1, 'str1' => 'abc1']);

        // Arrange
        $key = $t->name().':'.RedisDatabaseDriver::ID.':'.$id;
        $this->assertEquals(1, $this->redis->exists($key));
        $this->assertEquals(1, $this->redis->hget($key, 'int1'));
        $this->assertEquals('abc1', $this->redis->hget($key, 'str1'));
    }

    public function testInsertWhenHaveIndex()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1')->addIndex();
        $t->string('str1');
        $this->driver->create($t);

        // Act
        $this->driver->table($t->name())->insert(['int1' => 1, 'str1' => 'abc1']);

        // Arrange
        $this->assertEquals(1, $this->redis->exists($t->name().':int1:1'));
    }

    public function testInsertCorrectlyCreatesIdsKey()
    {
        // Arrange
        $this->redis = new Client(['database' => 1]);
        $t = new Table('simpleObject');
        $t->increments('id');
        $t->integer('int1');
        $this->driver->create($t);

        // Act
        $this->driver->table($t->name())->insert(['int1' => 1]);

        // Arrange
        $this->assertEquals(1, $this->redis->exists($t->name().':'.RedisDatabaseDriver::IDS));
    }
}
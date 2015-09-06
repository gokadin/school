<?php

namespace FrameworkTest\Console\DataMapper;

use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
use Library\Database\Column;
use Library\Database\Database;
use Library\Database\Schema;
use Library\Database\Table;
use Predis\Client;
use Tests\FrameworkTest\BaseTest;

class DataMapperRedisCacheDriverTest extends BaseTest
{
    protected $driver;
    protected $redis;
    protected $database;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new DataMapperRedisCacheDriver(2);

        $this->redis = new Client(['database' => 2]);

        $this->database = new Database([
            'driver' => 'mysql',
            'mysql' => [
                'host' => 'localhost',
                'database' => 'FrameworkTest',
                'username' => 'root',
                'password' => 'f10ygs87'
            ]
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->redis->flushdb();
    }

    public function testLoadSchemaCreatesCorrectModelTableRelationship()
    {
        // Arrange
        $schema = new Schema($this->database);
        $table = new Table('t1', 'model1');
        $table->increments('id');
        $schema->add($table);

        // Act
        $this->driver->loadSchema($schema);

        // Assert
        $this->assertEquals($table->name(), $this->redis->get($table->modelName().':table'));
    }

    public function testLoadSchemaCreatesCorrectColumnNamesSet()
    {
        // Arrange
        $schema = new Schema($this->database);
        $table = new Table('t1', 'model1');
        $table->increments('id');
        $table->integer('int1');
        $table->string('str1');
        $schema->add($table);

        // Act
        $this->driver->loadSchema($schema);

        // Assert
        $names = $this->redis->smembers(DataMapperRedisCacheDriver::SCHEMA.':'.$table->name().':columns');
        $this->assertEquals(3, sizeof($names));
        $this->assertTrue(in_array('id', $names));
        $this->assertTrue(in_array('int1', $names));
        $this->assertTrue(in_array('str1', $names));
    }

    public function testLoadSchemaCreatesCorrectlyCreatesColumns()
    {
        // Arrange
        $schema = new Schema($this->database);
        $table = new Table('t1', 'model1');
        $table->increments('id');
        $table->integer('int1')->addIndex()->nullable();
        $table->string('str1')->default('hello');
        $schema->add($table);

        // Act
        $this->driver->loadSchema($schema);

        // Assert
        $id = $this->redis->hgetall(DataMapperRedisCacheDriver::SCHEMA.':'.$table->name().':column:id');
        $int1 = $this->redis->hgetall(DataMapperRedisCacheDriver::SCHEMA.':'.$table->name().':column:int1');
        $str1 = $this->redis->hgetall(DataMapperRedisCacheDriver::SCHEMA.':'.$table->name().':column:str1');

        $this->assertEquals(1, $id['isPrimaryKey']);

        $this->assertEquals('integer', $int1['type']);
        $this->assertEquals(1, $int1['hasIndex']);
        $this->assertEquals(1, $int1['isNullable']);

        $this->assertEquals('string', $str1['type']);
        $this->assertEquals(1, $str1['isDefault']);
        $this->assertEquals('hello', $str1['defaultValue']);
    }

    public function testLoadSchemaCreatesPropertyNameCorrectly()
    {
        // Arrange
        $schema = new Schema($this->database);
        $table = new Table('t1', 'model1');
        $table->increments('id');
        $column = $table->integer('int1');
        $column->propertyName('someName');
        $schema->add($table);

        // Act
        $this->driver->loadSchema($schema);

        // Assert
        $int1 = $this->redis->hgetall(DataMapperRedisCacheDriver::SCHEMA.':'.$table->name().':column:int1');
        $this->assertEquals('someName', $int1['propertyName']);
    }
}
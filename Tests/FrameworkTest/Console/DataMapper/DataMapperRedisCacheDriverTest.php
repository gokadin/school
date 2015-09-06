<?php

namespace FrameworkTest\Console\DataMapper;

use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
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
        $table->integer('int1')->addIndex();
        $table->string('str1');
        $schema->add($table);

        // Act
        $this->driver->loadSchema($schema);

        // Assert
        $this->assertEquals($table->name(), $this->redis->get($table->modelName().':table'));
    }
}
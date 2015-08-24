<?php

namespace FrameworkTest\Database\Drivers;

use Library\Database\Drivers\PdoDatabaseDriver;
use Library\Database\Table;
use Tests\FrameworkTest\BaseTest;

class PdoDatabaseDriverTest extends BaseTest
{
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new PdoDatabaseDriver([
            'host' => 'localhost',
            'database' => 'FrameworkTest',
            'username' => 'root',
            'password' => 'f10ygs87'
        ]);
    }

    public function tearDown()
    {
        $this->driver->dropAll();
    }

    protected function createBasicTable()
    {
        $t = new Table('test');

        $t->increments('id');
        $t->integer('col1');
        $t->string('col2');

        $this->driver->create($t);

        return $t;
    }

    public function testCreate()
    {
        // Arrange
        $t = new Table('test');
        $t->increments('id');
        $t->integer('col1');
        $t->string('col2');

        // Act
        $result = $this->driver->create($t);

        // Assert
        $this->assertEquals(0, $result);
    }

    public function testInsert()
    {
        // Arrange
        $t = $this->createBasicTable();

        // Act
        $this->driver->table($t->name())->insert([
            'col1' => 1,
            'col2' => 'str1'
        ]);
        $result = $this->driver->table($t->name())->select();

        // Assert
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals(1, $result[0]['col1']);
        $this->assertEquals('str1', $result[0]['col2']);
    }

    public function testSelect()
    {
        // Arrange
        $t = $this->createBasicTable();
        $this->driver->table($t->name())->insert(['col1' => 1, 'col2' => 'str1']);
        $this->driver->table($t->name())->insert(['col1' => 2, 'col2' => 'str2']);

        // Act
        $result = $this->driver->table($t->name())->select();

        // Assert
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals(1, $result[0]['col1']);
        $this->assertEquals('str1', $result[0]['col2']);
        $this->assertEquals(2, $result[1]['col1']);
        $this->assertEquals('str2', $result[1]['col2']);
    }

    public function testSelectWithCustomFields()
    {
        // Arrange
        $t = $this->createBasicTable();
        $this->driver->table($t->name())->insert(['col1' => 1, 'col2' => 'str1']);

        // Act
        $result = $this->driver->table($t->name())->select(['col1']);

        // Assert
        $this->assertEquals(1, sizeof($result[0]));
        $this->assertEquals(1, $result[0]['col1']);
    }

    public function testWhere()
    {
        // Arrange
        $t = $this->createBasicTable();
        $this->driver->table($t->name())->insert(['col1' => 1, 'col2' => 'str1']);
        $this->driver->table($t->name())->insert(['col1' => 2, 'col2' => 'str2']);

        // Act
        $result = $this->driver->table($t->name())
            ->where('col1', 2)->select();

        // Assert
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals(2, $result[0]['col1']);
        $this->assertEquals('str2', $result[0]['col2']);
    }

    public function testUpdate()
    {
        // Arrange
        $t = $this->createBasicTable();
        $this->driver->table($t->name())->insert(['col1' => 1, 'col2' => 'str1']);

        // Act
        $this->driver->table($t->name())->update(['col1' => 10]);
        $updated = $this->driver->table($t->name())->select();

        // Assert
        $this->assertEquals(10, $updated[0]['col1']);
        $this->assertEquals('str1', $updated[0]['col2']);
    }

    public function testDelete()
    {
        // Arrange
        $t = $this->createBasicTable();
        $this->driver->table($t->name())->insert(['col1' => 1, 'col2' => 'str1']);

        // Act
        $this->driver->table($t->name())->delete();
        $result = $this->driver->table($t->name())->select();

        // Assert
        $this->assertEquals(0, sizeof($result));
    }
}
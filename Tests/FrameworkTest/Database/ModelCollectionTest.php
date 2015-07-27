<?php namespace Tests\FrameworkTest\Database;

use Library\Database\ModelCollection;
use Library\Facades\DB;
use Library\Testing\DatabaseTransactions;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Models\Test;

class ModelCollectionTest extends BaseTest
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->createApplication();

        $this->beginDatabaseTransaction();
    }

    public function testThatCollectionsAreBeingCorrectlyInitializedWithoutParameters()
    {
        // Arrange
        $collection = new ModelCollection();

        // Assert
        $this->assertNotNull($collection);
        $this->assertEquals(0, $collection->count());
    }

    public function testThatCollectionsAreBeingCorrectlyInitializedWithParameters()
    {
        // Arrange
        $collection = new ModelCollection([
            new Test(),
            new Test(),
            new Test()
        ]);

        // Assert
        $this->assertNotNull($collection);
        $this->assertEquals(3, $collection->count());
    }

    public function testThatFirstMethodIsReturningTheFirstModel()
    {
        // Arrange
        $collection = new ModelCollection([
            new Test(['col1' => 'str1', 'col2' => 10]),
            new Test(['col1' => 'str2', 'col2' => 11]),
            new Test(['col1' => 'str3', 'col2' => 12])
        ]);

        // Assert
        $this->assertNotNull($collection->first());
        $this->assertEquals('str1', $collection->first()->col1);
        $this->assertEquals(10, $collection->first()->col2);
    }

    public function testThatFirstMethodIsReturningDefaultValueIfCollectionIsEmpty()
    {
        // Arrange
        $collection = new ModelCollection();
        $testModel = new Test();

        // Assert
        $this->assertNull($collection->first());
        $this->assertEquals(10, $collection->first(10));
        $this->assertEquals($testModel, $collection->first($testModel));
    }

    public function testThatLastMethodIsReturningTheLastModel()
    {
        // Arrange
        $collection = new ModelCollection([
            new Test(['col1' => 'str1', 'col2' => 10]),
            new Test(['col1' => 'str2', 'col2' => 11]),
            new Test(['col1' => 'str3', 'col2' => 12])
        ]);

        // Assert
        $this->assertNotNull($collection->last());
        $this->assertEquals('str3', $collection->last()->col1);
        $this->assertEquals(12, $collection->last()->col2);
    }

    public function testThatLastMethodIsReturningDefaultValueIfCollectionIsEmpty()
    {
        // Arrange
        $collection = new ModelCollection();
        $testModel = new Test();

        // Assert
        $this->assertNull($collection->last());
        $this->assertEquals(10, $collection->last(10));
        $this->assertEquals($testModel, $collection->last($testModel));
    }

    public function testThatPrimaryKeyCanBeFoundInCollection()
    {
        // Arrange
        $test = new Test(['col1' => 'str1', 'col2' => 10]);
        $id = $test->id;
        $collection = new ModelCollection([$test]);

        // Assert
        $this->assertTrue($collection->hasPrimaryKey($id));
    }

    public function testThatModelCanBeFoundByPrimaryKey()
    {
        // Arrange
        $test1 = new Test(['col1' => 'str1', 'col2' => 10]);
        $test2 = new Test(['col1' => 'str1', 'col2' => 10]);
        $id = $test2->id;
        $test3 = new Test(['col1' => 'str1', 'col2' => 10]);
        $collection = new ModelCollection([$test1, $test2, $test3]);

        // Act
        $model = $collection->getPrimaryKey($id);

        // Assert
        $this->assertNotNull($model);
        $this->assertEquals($id, $model->id);
    }

    public function testJsonWorksCorrectly()
    {
        // Arrange
        $test1 = new Test(['col1' => 'str1', 'col2' => 11]);
        $test2 = new Test(['col1' => 'str2', 'col2' => 12]);
        $test3 = new Test(['col1' => 'str3', 'col2' => 13]);
        $collection = new ModelCollection([$test1, $test2, $test3]);

        // Act
        $json = json_encode($collection);
        $deserializedJson = json_decode($json, true);

        // Assert
        $this->assertEquals(3, count($deserializedJson));
        $this->assertEquals('str1', $deserializedJson[0]['col1']);
        $this->assertEquals('11', $deserializedJson[0]['col2']);
        $this->assertEquals('str2', $deserializedJson[1]['col1']);
        $this->assertEquals('12', $deserializedJson[1]['col2']);
        $this->assertEquals('str3', $deserializedJson[2]['col1']);
        $this->assertEquals('13', $deserializedJson[2]['col2']);
    }
}
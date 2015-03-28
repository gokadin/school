<?php namespace Tests\FrameworkTest\Database;

use Library\Database\ModelCollection;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Database\Models\Test;

class ModelCollectionTest extends BaseTest
{
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
}
<?php namespace Tests\FrameworkTest\Database;

use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Database\Models\Test;

class ModelTest extends BaseTest
{
    public function testThatNewModelCanBeProperlySaved()
    {
        // Arrange
        $test = new Test();
        $test->col1 = 'str';
        $test->col2 = 10;

        // Act
        $test->save();
        $id = $test->id;
        $test = Test::find($id);

        // Assert
        $this->assertEquals('str', $test->col1);
        $this->assertEquals(10, $test->col2);
    }

    public function testThatNewModelsCanBeProperlyCreated()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);

        // Act
        $id = $test->id;
        $test = Test::find($id);

        // Assert
        $this->assertEquals('str', $test->col1);
        $this->assertEquals(10, $test->col2);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThatNewModelsWithMissingRequiredFieldsCannotBeCreated()
    {
        // Arrange
        $test = Test::create(['col1' => 'str']);

        // Assert
        $this->assertNull($test);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThatNewModelsWithMissingRequiredFieldsCannotBeSaved()
    {
        // Arrange
        $test = new Test();

        // Act
        $test->col1 = 'str';
        $test->save();

        // Assert
        $this->assertNull($test);
    }

    public function testThatCreatedModelsAreProperlyUpdated()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);

        // Act
        $test->col1 = 'newStr';
        $test->save();

        // Assert
        $this->assertEquals('newStr', $test->col1);
        $this->assertEquals(10, $test->col2);
    }

    public function testThatTouchIsWorkingCorrectly()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);
        $updated_at = $test->updated_at;
        $created_at = $test->created_at;

        // Act
        sleep(1);
        $test->touch();

        // Assert
        $this->assertGreaterThan($updated_at, $test->updated_at);
        $this->assertEquals($created_at, $test->created_at);
        $this->assertEquals('str', $test->col1);
        $this->assertEquals(10, $test->col2);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThatNewModelsCannotBeDeleted()
    {
        // Arrange
        $test = new Test();
        $test->col1 = 'str';
        $test->col2 = 10;

        // Act
        $result = $test->delete();

        // Assert
        $this->assertFalse($result);
    }

    public function testThatCreatedModelsCanBeDeleted()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);

        // Act
        $result = $test->delete();

        // Assert
        $this->assertTrue($result);
    }

    public function testThatExistingColumnIsFoundByMethodExists()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);

        // Act
        $result = Test::exists('col1', 'str');

        // Assert
        $this->assertTrue($result);
    }
}
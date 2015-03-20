<?php namespace Tests\FrameworkTest\Database;

use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Database\Models\Test;
use Tests\FrameworkTest\Database\Models\Teacher;
use Tests\FrameworkTest\Database\Models\Student;

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

    public function testThatExistingModelsCanProperlyBeFound()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);
        $id = $test->id;

        // Act
        $test = Test::find($id);

        // Assert
        $this->assertNotNull($test);
    }

    public function testThatWhereClauseIsWorkingProperly()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);
        $id = $test->id;

        // Act
        $test = Test::where('id', '=', $id)->get();

        // Assert
        $this->assertNotNull($test);
        $test = $test->first();
        $this->assertEquals($id, $test->id);
    }

    public function testThatWhereClauseCanBeUsedInSuccession()
    {
        // Arrange
        $test = Test::create(['col1' => 'str', 'col2' => 10]);
        $id = $test->id;

        // Act
        $test = Test::where('id', '=', $id)
            ->where('col1', '=', 'str')
            ->where('col2', '>=', 10)->get();

        // Assert
        $this->assertNotNull($test);
        $test = $test->first();
        $this->assertEquals($id, $test->id);
    }

    /* RELATIONSHIPS */

    public function testThatHasOneRelationshipIsWorkingCorrectly()
    {
        // Arrange
        $teacher = Teacher::create(['name' => 'teacherName']);
        $teacher_id = $teacher->id;
        $student = Student::create(['name' => 'studentName', 'teacher_id' => $teacher_id]);

        // Act
        $resolvedTeacher = $student->teacher();

        // Assert
        $this->assertNotNull($resolvedTeacher);
        $this->assertEquals($teacher_id, $resolvedTeacher->id);
    }

    public function testThatHasManyRelationshipIsWorkingCorrectly()
    {
        // Arrange
        $teacher = Teacher::create(['name' => 'teacherName']);
        $student1 = Student::create(['name' => 'studentName1', 'teacher_id' => $teacher->id]);
        $student2 = Student::create(['name' => 'studentName2', 'teacher_id' => $teacher->id]);
        $student3 = Student::create(['name' => 'studentName3', 'teacher_id' => $teacher->id]);

        // Act
        $students = $teacher->students();

        // Assert
        $this->assertNotNull($students);
        $this->assertEquals(3, $students->count());
        $this->assertEquals('studentName1', $students->first()->name);
        $this->assertEquals('studentName2', $students->at(1)->name);
        $this->assertEquals('studentName3', $students->last()->name);
    }
}
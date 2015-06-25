<?php namespace Tests\FrameworkTest\Database;

use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Models\Activity;
use Tests\FrameworkTest\Models\Post;
use Tests\FrameworkTest\Models\School;
use Tests\FrameworkTest\Models\Test;
use Tests\FrameworkTest\Models\Teacher;
use Tests\FrameworkTest\Models\Student;
use Tests\FrameworkTest\Models\Address;
use Tests\FrameworkTest\Models\ActivityStudent;

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
        sleep(2);
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
        $x = $this->getRowCount('tests');
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

    public function testThatJoinClauseIsWorkingProperlyWhenReturningModels()
    {
        // Arrange
        $school = School::create(['name' => 'schoolName']);
        Teacher::create(['school_id' => $school->id, 'name' => 'teacherName']);

        // Act
        $results = School::join('teachers')
            ->where('teachers.school_id', '=', $school->id)
            ->get();

        // Assert
        $this->assertEquals(1, $results->count());
        $this->assertEquals($school->id, $results->first()->school_id);
    }

    public function testThatJoinClauseIsWorkingProperlyWhenReturningArrays()
    {
        // Arrange
        $school = School::create(['name' => 'schoolName']);
        Teacher::create(['school_id' => $school->id, 'name' => 'teacherName']);

        // Act
        $results = School::join('teachers')
            ->where('teachers.school_id', '=', $school->id)
            ->get(['schools.name as name', 'teachers.school_id']);

        // Assert
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals($school->id, $results[0]['school_id']);
        $this->assertEquals('schoolName', $results[0]['name']);
    }

    public function testThatAllMethodIsWorkingCorrectly()
    {
        // Arrange
        Test::create(['col1' => 'one', 'col2' => 1]);
        Test::create(['col1' => 'two', 'col2' => 2]);
        Test::create(['col1' => 'three', 'col2' => 3]);

        // Act
        $results = Test::all();// Assert
        $this->assertGreaterThanOrEqual(3, $results->count());
    }

    /**
     * @group relationships
     */
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

    /**
     * @group relationships
     */
    public function testThatHasManyRelationshipIsWorkingCorrectly()
    {
        // Arrange
        $teacher = Teacher::create(['name' => 'teacherName']);
        Student::create(['name' => 'studentName1', 'teacher_id' => $teacher->id]);
        Student::create(['name' => 'studentName2', 'teacher_id' => $teacher->id]);
        Student::create(['name' => 'studentName3', 'teacher_id' => $teacher->id]);

        // Act
        $students = $teacher->students();

        // Assert
        $this->assertNotNull($students);
        $this->assertEquals(3, $students->count());
        $this->assertEquals('studentName1', $students->first()->name);
        $this->assertEquals('studentName2', $students->at(1)->name);
        $this->assertEquals('studentName3', $students->last()->name);
    }

    /**
     * @group relationships
     */
    public function testThatBelongsToRelationshipIsWorkingCorrectly()
    {
        // Arrange
        $address = Address::create(['country' => 'Canada']);
        $teacher = Teacher::create(['address_id' => $address->id, 'name' => 'teacherName']);

        // Act
        $resolvedTeacher = $address->teacher();

        // Assert
        $this->assertNotNull($resolvedTeacher);
        $this->assertEquals($teacher->id, $resolvedTeacher->id);
    }

    /**
     * @group relationships
     */
    public function testThatHasManyThroughRelationshipIsWorkingCorrectly()
    {
        // Arrange
        $school = School::create(['name' => 'schoolName']);
        $student = Student::create(['teacher_id' => 0, 'school_id' => $school->id, 'name' => 'studentName']);
        Post::create(['student_id' => $student->id, 'title' => 'title1']);
        Post::create(['student_id' => $student->id, 'title' => 'title2']);
        Post::create(['student_id' => $student->id, 'title' => 'title3']);

        // Act
        $resolvedPosts = $school->posts();

        // Assert
        $this->assertEquals(3, $resolvedPosts->count());
        $this->assertEquals('title1', $resolvedPosts->first()->title);
        $this->assertEquals('title2', $resolvedPosts->at(1)->title);
        $this->assertEquals('title3', $resolvedPosts->last()->title);
    }

    /**
     * @group relationships
     */
    public function testThatManyToManyRelationshipsAreWorkingCorrectly()
    {
        // Arrange
        $teacher_id = Teacher::create(['name' => 'teacherName'])->id;
        $student1 = Student::create(['teacher_id' => $teacher_id, 'name' => 'name1']);
        $student2 = Student::create(['teacher_id' => $teacher_id, 'name' => 'name2']);
        $student3 = Student::create(['teacher_id' => $teacher_id, 'name' => 'name3']);
        $activity1 = Activity::create(['name' => 'name1']);
        $activity2 = Activity::create(['name' => 'name2']);
        $activity3 = Activity::create(['name' => 'name3']);

        // Act
        ActivityStudent::create(['activity_id' => $activity1->id, 'student_id' => $student1->id]);
        ActivityStudent::create(['activity_id' => $activity1->id, 'student_id' => $student2->id]);
        ActivityStudent::create(['activity_id' => $activity1->id, 'student_id' => $student3->id]);
        ActivityStudent::create(['activity_id' => $activity2->id, 'student_id' => $student1->id]);
        ActivityStudent::create(['activity_id' => $activity3->id, 'student_id' => $student1->id]);

        $resolvedStudents = $activity1->students();
        $resolvedActivities = $student1->activities();

        // Assert
        $this->assertNotNull($resolvedStudents);
        $this->assertEquals($student1->id, $resolvedStudents->first()->id);
        $this->assertEquals($student2->id, $resolvedStudents->at(1)->id);
        $this->assertEquals($student3->id, $resolvedStudents->last()->id);
        $this->assertEquals($activity1->id, $resolvedActivities->first()->id);
        $this->assertEquals($activity2->id, $resolvedActivities->at(1)->id);
        $this->assertEquals($activity3->id, $resolvedActivities->last()->id);
    }
}
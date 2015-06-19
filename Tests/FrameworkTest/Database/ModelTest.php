<?php namespace Tests\FrameworkTest\Database;

use Library\Facades\DB;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Database\Models\Activity;
use Tests\FrameworkTest\Database\Models\Animal;
use Tests\FrameworkTest\Database\Models\Lion;
use Tests\FrameworkTest\Database\Models\Post;
use Tests\FrameworkTest\Database\Models\School;
use Tests\FrameworkTest\Database\Models\Test;
use Tests\FrameworkTest\Database\Models\Teacher;
use Tests\FrameworkTest\Database\Models\Student;
use Tests\FrameworkTest\Database\Models\Address;
use Tests\FrameworkTest\Database\Models\ActivityStudent;

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

    /**
     * @group polymorphism
     */
    public function testThatWhenCreatingAPolymorphicModelThenBaseModelIsAlsoCreated()
    {
        // Arrange
        $animalRowCountBefore = $this->getRowCount('animals');
        Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);
        $animalRowCountAfter = $this->getRowCount('animals');

        // Assert
        $this->assertEquals($animalRowCountBefore + 1, $animalRowCountAfter);
    }

    /**
     * @group polymorphism
     */
    public function testThatWhenCreatingAPolymorphicModelTheMetaIdAndMetaTypeFieldsAreCorrectlySaved()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Assert
        $this->assertNotEmpty($lion->meta_id);
        $this->assertEquals('Lion', $lion->meta_type);
    }

    /**
     * @group polymorphism
     * @group x
     */
    public function testThatWhenAccessingIdOfPolymorphicModelThenThenBaseModelIdIsReturned()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Act
        $lion->baseModel()->id = 'someId';

        // Assert
        $this->assertEquals('someId', $lion->id);
    }

    /**
     * @group polymorphism
     * @expectedException RuntimeException
     */
    public function testThatAPolymorphicModelCannotBeCreatedIfARequiredFieldIsMissingOnlyInThatModel()
    {
        // Arrange
        $lionRowCountBefore = DB::query('SELECT COUNT(*) FROM lions')->fetchColumn();
        $animalRowCountBefore = DB::query('SELECT COUNT(*) FROM animals')->fetchColumn();
        $lion = Lion::create(['animalCol1' => 'bstr']);
        $lionRowCountAfter = DB::query('SELECT COUNT(*) FROM lions')->fetchColumn();
        $animalRowCountAfter = DB::query('SELECT COUNT(*) FROM animals')->fetchColumn();

        // Assert
        $this->assertNull($lion);
        $this->assertEquals($lionRowCountBefore, $lionRowCountAfter);
        $this->assertEquals($animalRowCountBefore, $animalRowCountAfter);
    }

    /**
     * @group polymorphism
     * @expectedException RuntimeException
     */
    public function testThatAPolymorphicModelCannotBeCreatedIfARequiredFieldIsMissingInTheBaseModel()
    {
        // Arrange
        $lionRowCountBefore = $this->getRowCount('lions');
        $animalRowCountBefore = $this->getRowCount('animals');
        $lion = Lion::create(['lionCol1' => 'pstr']);
        $lionRowCountAfter = $this->getRowCount('lions');
        $animalRowCountAfter = $this->getRowCount('animals');

        // Assert
        $this->assertNull($lion);
        $this->assertEquals($lionRowCountBefore, $lionRowCountAfter);
        $this->assertEquals($animalRowCountBefore, $animalRowCountAfter);
    }

    /**
     * @group polymorphism
     */
    public function testThatThePolymorhicModelPropertiesCanBeProperlyAccessed()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 10, 'animalCol1' => 'bstr', 'animalCol2' => 11]);

        // Assert
        $this->assertEquals('pstr', $lion->lionCol1);
        $this->assertEquals(10, $lion->lionCol2);
    }

    /**
     * @group polymorphism
     */
    public function testThatTheBaseModelPropertiesCanBeProperlyAccessed()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 10, 'animalCol1' => 'bstr', 'animalCol2' => 11]);

        // Assert
        $this->assertEquals('bstr', $lion->animalCol1);
        $this->assertEquals(11, $lion->animalCol2);
    }

    /**
     * @group polymorphism
     */
    public function testThatThePolymorhicModelPropertiesCanBeProperlySet()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Act
        $lion->lionCol1 = 'different';
        $lion->lionCol2 = 20;

        // Assert
        $this->assertEquals('different', $lion->lionCol1);
        $this->assertEquals(20, $lion->lionCol2);
    }

    /**
     * @group polymorphism
     */
    public function testThatTheBaseModelPropertiesCanBeProperlySet()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Act
        $lion->animalCol1 = 'different';
        $lion->animalCol2 = 20;

        // Assert
        $this->assertEquals('different', $lion->animalCol1);
        $this->assertEquals(20, $lion->animalCol2);
    }

    /**
     * @group polymorphism
     */
    public function testThatWhenSavingANewPolymorphicModelThenBaseModelIsAlsoSaved()
    {
        // Arrange
        $lion = new Lion();
        $lion->lionCol1 = 'pstr';
        $lion->animalCol1 = 'bstr';

        // Act
        $lionRowCountBefore = $this->getRowCount('lions');
        $animalRowCountBefore = $this->getRowCount('animals');
        $lion->save();
        $id = $lion->baseModel()->id;
        $lionRowCountAfter = $this->getRowCount('lions');
        $animalRowCountAfter = $this->getRowCount('animals');

        // Assert
        $this->assertEquals($lionRowCountBefore + 1, $lionRowCountAfter);
        $this->assertEquals($animalRowCountBefore + 1, $animalRowCountAfter);
        $this->assertTrue(Animal::exists('id', $id));
    }

    /**
     * @group polymorphism
     */
    public function testThatWhenSavingACreatedPolymorphicModelThenBaseModelIsAlsoSaved()
    {
        // Arrange
        $lion = new Lion();
        $lion->lionCol1 = 'pstr';
        $lion->animalCol1 = 'bstr';

        // Act
        $lion->save();
        $lion->lionCol1 = 'different';
        $lion->animalCol1 = 'different';
        $lion->animalCol2 = 20;
        $updateResult = $lion->save();
        $newLion = Lion::find($lion->id);

        // Assert
        $this->assertTrue($updateResult);
        $this->assertEquals('different', $newLion->lionCol1);
        $this->assertEquals('different', $newLion->animalCol1);
        $this->assertEquals(20, $newLion->animalCol2);
    }

    /**
     * @group polymorphism
     */
    public function testThatWhenDeletingAPolymorphicModelThenBaseModelIsAlsoDeleted()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);
        $id = $lion->id;

        // Act
        $lionRowCountBefore = $this->getRowCount('lions');
        $lion->delete();
        $lionRowCountAfter = $this->getRowCount('lions');

        // Assert
        $this->assertEquals($lionRowCountBefore - 1, $lionRowCountAfter);
        $this->assertFalse(Animal::exists('id', $id));
    }

    /**
     * @group polymorphism
     */
    public function testThatExistsMethodFindsAPolymorphicModelColumn()
    {
        // Arrange
        Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Assert
        $this->assertTrue(Lion::exists('lionCol1', 'pstr'));
    }

    /**
     * @group polymorphism
     */
    public function testThatExistsMethodFindsABaseModelColumn()
    {
        // Arrange
        Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Assert
        $this->assertTrue(Lion::exists('animalCol1', 'bstr'));
    }

    /**
     * @group polymorphism
     */
    public function testThatFindMethodAlsoFindsAndPopulatesTheBaseModel()
    {
        // Arrange
        $lion = Lion::create(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Act
        $newLion = Lion::find($lion->id);

        // Assert
        $this->assertNotNull($lion);
        $this->assertEquals('pstr', $newLion->lionCol1);
        $this->assertEquals('bstr', $newLion->animalCol1);
    }

    /**
     * @group polymorphism
     */
    public function testThatCustomBaseModelFunctionsAreAccessibleFromThePolymorphicModel()
    {
        // Arrange
        $lion = new Lion(['lionCol1' => 'pstr', 'animalCol1' => 'bstr']);

        // Assert
        $this->assertEquals('BSTR', $lion->uppercaseCol1());
    }

    /**
     * @group polymorphism
     */
    public function testThatAllMethodAlsoCorrectlyFindsAndPopulatesBaseModels()
    {
        // Arrange
        Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 36, 'animalCol1' => 'bstr']);

        // Act
        $lions = Lion::all();

        // Assert
        $this->assertTrue($lions->count() > 0);
        $this->assertNotNull($lions->first()->animalCol1);
        $this->assertNotEmpty($lions->first()->meta_id);
    }

    /**
     * @group polymorphism
     */
    public function testWhereMethodForPolymorphicFieldsOnlyWhenReturningModels()
    {
        // Arrange
        $id = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 32, 'animalCol1' => 'bstr'])->id;

        // Act
        $lions = Lion::where('lionCol2', '=', 32)
            ->where('lionCol1', '=', 'pstr')
            ->where('id', '=', $id)
            ->get();

        // Assert
        $this->assertEquals(1, $lions->count());
        $this->assertEquals($id, $lions->first()->id);
        $this->assertEquals('pstr', $lions->first()->lionCol1);
        $this->assertEquals(32, $lions->first()->lionCol2);
        $this->assertEquals('bstr', $lions->first()->animalCol1);
    }

    /**
     * @group polymorphism
     */
    public function testWhereMethodForBaseFieldsOnlyWhenReturningModels()
    {
        // Arrange
        $id = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 1, 'animalCol1' => 'bstr', 'animalCol2' => 188])->id;

        // Act
        $lions = Lion::where('animalCol2', '=', 188)->get();

        // Assert
        $this->assertEquals(1, $lions->count());
        $this->assertEquals($id, $lions->first()->id);
        $this->assertEquals('pstr', $lions->first()->lionCol1);
        $this->assertEquals(1, $lions->first()->lionCol2);
        $this->assertEquals('bstr', $lions->first()->animalCol1);
    }

    /**
     * @group polymorphism
     */
    public function testWhereMethodForPolymorphicAndBaseFieldsWhenReturningModels()
    {
        // Arrange
        Lion::create(['lionCol1' => 'dummy', 'lionCol2' => 1, 'animalCol1' => 'dummy', 'animalCol2' => 1]);
        Lion::create(['lionCol1' => 'dummy', 'lionCol2' => 1, 'animalCol1' => 'dummy', 'animalCol2' => 1]);
        $id1 = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 61, 'animalCol1' => 'bstr', 'animalCol2' => 108])->id;
        $id2 = Lion::create(['lionCol1' => 'pstr', 'lionCol2' => 62, 'animalCol1' => 'bstr', 'animalCol2' => 109])->id;
        Lion::create(['lionCol1' => 'dummy', 'lionCol2' => 1, 'animalCol1' => 'dummy', 'animalCol2' => 1]);
        Lion::create(['lionCol1' => 'dummy', 'lionCol2' => 1, 'animalCol1' => 'dummy', 'animalCol2' => 1]);

        // Act
        $lions1 = Lion::where('lionCol2', '=', 61)
            ->where('animalCol2', '=', 108)
            ->get();
        $lions2 = Lion::where('lionCol2', '=', 61)
            ->orWhere('animalCol2', '=', 109)
            ->get();

        // Assert
        $this->assertEquals(1, $lions1->count());
        $this->assertEquals($id1, $lions1->first()->id);
        $this->assertEquals(2, $lions2->count());
        $this->assertEquals($id1, $lions2->first()->id);
        $this->assertEquals($id2, $lions2->last()->id);
    }

    /**
     * @group polymorphism
     */
    public function testWhereMethodForPolymorphicAndBaseFieldsWhenResultsAreNullWithoutValues()
    {
        // Act
        $lionsWhereP = Lion::where('lionCol1', '=', 'non existant')
            ->get();
        $lionsWhereB = Lion::where('animalCol1', '=', 'non existant')
            ->get();
        $lionsWhereBoth = Lion::where('lionCol1', '=', 'non existant')
            ->where('animalCol1', '=', 'non existant')
            ->get();
        $lionsOrWhereBoth = Lion::where('lionCol1', '=', 'non existant')
            ->orWhere('animalCol1', '=', 'non existant')
            ->get();

        // Assert
        $this->assertEquals(0, $lionsWhereP->count());
        $this->assertEquals(0, $lionsWhereB->count());
        $this->assertEquals(0, $lionsWhereBoth->count());
        $this->assertEquals(0, $lionsOrWhereBoth->count());
    }

//    /**
//     * @group polymorphism
//     *
//     */
//    public function testWhereMethodForPolymorphicAndBaseFieldsWhenResultsAreNullWithValues()
//    {
//        // Act
//        $lionsWherePValueP = Lion::where('lionCol1', '=', 'non existant')
//            ->get(['lionCol2']);
//        $lionsWherePValueB = Lion::where('lionCol1', '=', 'non existant')
//            ->get(['animalCol2']);
//        $lionsWherePValueBoth = Lion::where('lionCol1', '=', 'non existant')
//            ->get(['lionCol2', 'animalCol2']);
//        $lionsWhereBValueP = Lion::where('animalCol1', '=', 'non existant')
//            ->get(['lionCol2']);
//        $lionsWhereBValueB = Lion::where('animalCol1', '=', 'non existant')
//            ->get(['animalCol2']);
//        $lionsWhereBValueBoth = Lion::where('animalCol1', '=', 'non existant')
//            ->get(['lionCol2', 'animalCol2']);
//        $lionsWhereBothValueP = Lion::where('lionCol1', '=', 'non existant')
//            ->where('animalCol1', '=', 'non existant')
//            ->get(['lionCol2']);
//        $lionsWhereBothValueB = Lion::where('lionCol1', '=', 'non existant')
//            ->where('animalCol1', '=', 'non existant')
//            ->get(['animalCol2']);
//        $lionsWhereBothValueBoth = Lion::where('lionCol1', '=', 'non existant')
//            ->where('animalCol1', '=', 'non existant')
//            ->get(['lionCol2', 'animalCol2']);
//
//        // Assert
//        $this->assertEquals(0, sizeof($lionsWherePValueP));
//        $this->assertEquals(0, sizeof($lionsWherePValueB));
//        $this->assertEquals(0, sizeof($lionsWherePValueBoth));
//        $this->assertEquals(0, sizeof($lionsWhereBValueP));
//        $this->assertEquals(0, sizeof($lionsWhereBValueB));
//        $this->assertEquals(0, sizeof($lionsWhereBValueBoth));
//        $this->assertEquals(0, sizeof($lionsWhereBothValueP));
//        $this->assertEquals(0, sizeof($lionsWhereBothValueB));
//        $this->assertEquals(0, sizeof($lionsWhereBothValueBoth));
//    }
}
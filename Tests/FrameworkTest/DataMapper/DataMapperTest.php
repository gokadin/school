<?php

namespace Tests\FrameworkTest\DataMapper;

use Library\DataMapper\Collection\PersistentCollection;
use Library\DataMapper\EntityCollection;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;
use Tests\FrameworkTest\TestData\DataMapper\Teacher;
use Tests\FrameworkTest\TestData\DataMapper\Student;

class DataMapperTest extends DataMapperBaseTest
{
    public function testPersistWhenInserting()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(1, $results[0]['one']);
    }

    public function testPersistWhenUpdating()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(1, $results[0]['one']);

        // Act
        $se->setOne(10);
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(10, $results[0]['one']);
    }

    public function testDeleteWhenPassingClassNameAndId()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete(get_class($se), $se->getId());

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }

    public function testDeleteWhenPassingObject()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }

    public function testFind()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se);

        // Act
        $result = $this->dm->find(SimpleEntity::class, $se->getId());

        // Assert
        $this->assertTrue($result instanceof SimpleEntity);
        $this->assertEquals($se->getId(), $result->getId());
    }

    public function testFindWhenNotFound()
    {
        // Arrange
        $this->setUpSimpleEntity();

        // Act
        $result = $this->dm->find(SimpleEntity::class, 1);

        // Assert
        $this->assertNull($result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOrFailWhenNotFound()
    {
        // Arrange
        $this->setUpSimpleEntity();

        // Act
        $result = $this->dm->findOrFail(SimpleEntity::class, 1);

        // Assert
        $this->assertNull($result);
    }

    public function testFindAll()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $this->dm->persist(new SimpleEntity(1, 2, 'one', 'two'));
        $this->dm->persist(new SimpleEntity(11, 12, 'one2', 'two2'));
        $this->dm->persist(new SimpleEntity(21, 22, 'one3', 'two3'));

        // Act
        $collection = $this->dm->findAll(SimpleEntity::class);

        // Assert
        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->first() instanceof SimpleEntity);
    }

    public function testFindBy()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(11, 12, 'one2', 'two2');
        $this->dm->persist($se2);

        // Act
        $collection = $this->dm->findBy(SimpleEntity::class, ['one' => 11]);

        // Assert
        $this->assertEquals(1, $collection->count());
        $this->assertEquals(11, $collection->first()->getOne());
    }

    public function testFindByWithMultipleEntitiesFound()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(1, 12, 'one', 'two2');
        $this->dm->persist($se2);

        // Act
        $collection = $this->dm->findBy(SimpleEntity::class, ['one' => 1, 'str1' => 'one']);

        // Assert
        $this->assertEquals(2, $collection->count());
    }

    public function testFindOneBy()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(1, 12, 'one', 'two2');
        $this->dm->persist($se2);

        // Act
        $entities = $this->dm->findOneBy(SimpleEntity::class, ['one' => 1, 'str1' => 'one']);

        // Assert
        $this->assertEquals(1, sizeof($entities));
    }

    public function testBelongsToWhenInserting()
    {
        // Arrange
        $this->setUpAssociations();
        $teacher = new Teacher('ateacher');
        $this->dm->persist($teacher);
        $student = new Student('astudent', $teacher);
        $this->dm->persist($student);

        // Act
        $student = $this->dm->find(Student::class, $student->getId());

        // Assert
        $this->assertNotNull($student->teacher());
        $this->assertEquals('ateacher', $student->teacher()->name());
    }

    public function testBelongsToWhenInsertingNonPersistedAssociation()
    {
        // Arrange
        $this->setUpAssociations();
        $student = new Student('astudent', new Teacher('ateacher'));
        $this->dm->persist($student);

        // Act
        $student = $this->dm->find(Student::class, $student->getId());

        // Assert
        $this->assertNotNull($student->teacher());
        $this->assertEquals('ateacher', $student->teacher()->name());
    }

    public function testBelongsToWhenChangingEntityAndUpdating()
    {
        // Arrange
        $this->setUpAssociations();
        $student = new Student('student1', new Teacher('teacher1'));
        $this->dm->persist($student);

        // Act
        $student = $this->dm->find(Student::class, $student->getId());

        // Assert
        $this->assertEquals('teacher1', $student->teacher()->name());

        // Act
        $student->setTeacher(new Teacher('teacher2'));
        $this->dm->persist($student);
        $student = $this->dm->find(Student::class, $student->getId());

        // Assert
        $this->assertEquals('teacher2', $student->teacher()->name());
    }

    public function testHasManyWhenHaveNoStudents()
    {
        // Arrange
        $this->setUpAssociations();
        $teacher = new Teacher('teacher1');

        // Act
        $this->dm->persist($teacher);
        $teacher = $this->dm->find(Teacher::class, $teacher->getId());

        // Assert
        $this->assertTrue($teacher->students() instanceof PersistentCollection);
        $this->assertTrue($teacher->students()->isEmpty());
    }

    public function testHasManyWhenInserting()
    {
        // Arrange
        $this->setUpAssociations();
        $teacher = new Teacher('teacher1');
        $student1 = new Student('student1', $teacher);
        $student2 = new Student('student2', $teacher);
        $student3 = new Student('student3', $teacher);
        $this->dm->persist($student1);
        $this->dm->persist($student2);
        $this->dm->persist($student3);
        $teacher->addStudent($student1);
        $teacher->addStudent($student2);
        $teacher->addStudent($student3);

        // Act
        $this->dm->persist($teacher);
        $teacher = $this->dm->find(Teacher::class, $teacher->getId());

        // Assert
        $this->assertTrue($teacher->students() instanceof PersistentCollection);
        $this->assertEquals(3, $teacher->students()->count());
    }

    public function testHasManyWhenInsertingNonPersistedEntities()
    {
        // Arrange
        $this->setUpAssociations();
        $teacher = new Teacher('teacher1');
        $student1 = new Student('student1', $teacher);
        $student2 = new Student('student2', $teacher);
        $student3 = new Student('student3', $teacher);
        $teacher->addStudent($student1);
        $teacher->addStudent($student2);
        $teacher->addStudent($student3);

        // Act
        $this->dm->persist($teacher);
        $teacher = $this->dm->find(Teacher::class, $teacher->getId());

        // Assert
        $this->assertTrue($teacher->students() instanceof PersistentCollection);
        $this->assertEquals(3, $teacher->students()->count());
        $this->assertEquals($teacher->getId(), $teacher->students()->first()->teacher()->getId());
    }

    public function testHasManyWhenAddingANewEntityAndUpdating()
    {

    }

    public function testHasManyWhenAddingANewNonPersistedEntityAndUpdating()
    {

    }

    public function testHasManyWhenRemovingNewEntityAndUpdating()
    {

    }
}
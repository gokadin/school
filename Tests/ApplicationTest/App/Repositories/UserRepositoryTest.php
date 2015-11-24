<?php

namespace ApplicationTest\App\Repositories;

use App\Repositories\UserRepository;
use Tests\ApplicationTest\BaseTest;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use App\Domain\Common\Address;
use App\Domain\School\School;

class UserRepositoryTest extends BaseTest
{
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->setUpDatamapper([
            Subscription::class,
            TempTeacher::class,
            Teacher::class,
            Address::class,
            School::class
        ]);

        $this->repository = new UserRepository($this->dm);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->database->dropAll();
    }

    public function testPreRegisterTeacher()
    {
        // Act
        $tempTeacher = $this->repository->preRegisterTeacher([
            'subscriptionType' => 1,
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'email@one.com'
        ]);

        $this->dm->detachAll();
        $tempTeacher = $this->dm->find(TempTeacher::class, $tempTeacher->getId());

        // Assert
        $this->assertNotNull($tempTeacher);
        $this->assertNotNull($tempTeacher->subscription());
    }

    public function testRegisterTeacher()
    {
        // Arrange
        $tempTeacher = $this->repository->preRegisterTeacher([
            'subscriptionType' => 1,
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'email@one.com'
        ]);

        // Act
        $teacher = $this->repository->registerTeacher([
            'tempTeacherId' => $tempTeacher->getId(),
            'password' => 'admin'
        ]);

        $this->dm->detachAll();
        $teacher = $this->dm->find(Teacher::class, $teacher->getId());
        $tempTeacher = $this->dm->find(TempTeacher::class, $tempTeacher->getId());

        // Assert
        $this->assertNotNull($teacher);
        $this->assertNotNull($teacher->address());
        $this->assertNotNull($teacher->school());
        $this->assertNotNull($teacher->school()->address());
        $this->assertEquals(1, $teacher->subscription()->type());
        $this->assertEquals('fname', $teacher->firstName());
        $this->assertEquals('lname', $teacher->lastName());
        $this->assertEquals('email@one.com', $teacher->email());
        $this->assertEquals(md5('admin'), $teacher->password());
        $this->assertNull($tempTeacher);
    }

    public function testLoginTeacher()
    {
        // Arrange
        $tempTeacher = $this->repository->preRegisterTeacher([
            'subscriptionType' => 1,
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'email@one.com'
        ]);
        $teacher = $this->repository->registerTeacher([
            'tempTeacherId' => $tempTeacher->getId(),
            'password' => 'admin'
        ]);

        // Act
        $this->repository->loginTeacher($teacher);

        // Assert
        $this->assertTrue(isset($_SESSION['id']));
        $this->assertEquals($teacher->getId(), $_SESSION['id']);
    }
}
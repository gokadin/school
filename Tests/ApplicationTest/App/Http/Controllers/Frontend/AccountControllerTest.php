<?php

namespace Tests\ApplicationTest\App\Http\Controllers\Frontend;

use Library\Facades\Redirect;
use Library\Facades\Sentry;
use Models\Student;
use Models\Teacher;
use Tests\ApplicationTest\BaseTest;
use Library\Facades\ModelFactory as Factory;

class AccountControllerTest extends BaseTest
{
    public function testLoginWithTeacherWhenValid()
    {
        // Arrange
        $this->beginDatabaseTransaction();

        Redirect::shouldReceive('to')
            ->once()
            ->with('school.teacher.index.index');

        Factory::of(Teacher::class)->create(1, [
            'email' => 'a@b.com',
            'password' => md5('admin')
        ]);

        // Act
        $this->post('frontend.account.login', [
            'email' => 'a@b.com',
            'password' => 'admin'
        ]);

        // Assert
        $this->assertTrue(Sentry::loggedIn());
        $this->assertEquals('Teacher', Sentry::type());
    }

    public function testLoginWithStudentWhenValid()
    {
        // Arrange
        $this->beginDatabaseTransaction();

        Redirect::shouldReceive('to')
            ->once()
            ->with('school.student.index.index');

        Factory::of(Student::class)->create(1, [
            'email' => 'a@b.com',
            'password' => md5('admin')
        ]);

        // Act
        $this->post('frontend.account.login', [
            'email' => 'a@b.com',
            'password' => 'admin'
        ]);

        // Assert
        $this->assertTrue(Sentry::loggedIn());
        $this->assertEquals('Student', Sentry::type());
    }

    public function testLoginWhenInvalid()
    {
        // Arrange
        Redirect::shouldReceive('back')
            ->once();

        // Act
        $this->post('frontend.account.login', [
            'email' => 'a@b.com',
            'password' => 'admin'
        ]);

        // Assert
        $this->assertFalse(Sentry::loggedIn());
    }
}
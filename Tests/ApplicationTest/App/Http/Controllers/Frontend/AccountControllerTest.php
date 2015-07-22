<?php

namespace Tests\ApplicationTest\App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\AccountController;
use Models\Teacher;
use Tests\FrameworkTest\BaseTest;
use Library\Facades\ModelFactory as Factory;

class AccountControllerTest extends BaseTest
{
    public function testLogin()
    {
        // Arrange
        Factory::of(Teacher::class)->create(1, [
            'email' => 'a@b.com',
            'password' => md5('admin')
        ]);

        // Act

        // Assert
        $this->assertEquals('a@b.com', Teacher::all()->first()->email);
    }
}
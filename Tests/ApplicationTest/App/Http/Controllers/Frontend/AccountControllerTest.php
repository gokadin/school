<?php

namespace Tests\ApplicationTest\App\Http\Controllers\Frontend;

use Models\Teacher;
use Tests\ApplicationTest\BaseTest;
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
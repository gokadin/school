<?php

namespace Tests\ApplicationTest\App\Http\Controllers\Frontend;

use Library\Facades\Redirect;
use Library\Facades\Session;
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
        $this->action('POST', 'Frontend\\AccountController@login', [
            'email' => 'a@b.com',
            'password' => 'admin'
        ]);

        // Assert
        $this->assertTrue(Session::loggedIn());
    }
}
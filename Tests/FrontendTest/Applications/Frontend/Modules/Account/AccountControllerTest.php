<?php namespace Tests\FrontendTest\Applications\Frontend\Modules\Account;

use Applications\Frontend\Modules\Account\AccountController;
use Tests\FrontendTest\BaseTest;

class AccountControllerTest extends BaseTest
{
    public function testRegistrationWhenValid()
    {
        // Arrange
        $controller = new AccountController();
        $_POST['_method'] = 'POST';
        $_POST['firstName'] = 'fname';
        $_POST['lastName'] = 'lname';
        $_POST['email'] = 'some@email.com';

        // Act
        $controller->registerUser();

        // Assert
        $this->assertTrue(false);
    }
}
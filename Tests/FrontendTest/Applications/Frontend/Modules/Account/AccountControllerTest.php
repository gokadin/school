<?php namespace Tests\FrontendTest\Applications\Frontend\Modules\Account;

use Applications\Frontend\Modules\Account\AccountController;
use Library\Facades\App;
use Library\Facades\DB;
use Models\Subscription;
use Models\TempTeacher;
use Tests\FrontendTest\BaseTest;

class AccountControllerTest extends BaseTest
{
    public function setUp()
    {
        $_POST['_token'] = \Library\Facades\Session::generateToken();

        $response = $this->getMock('\\Library\\Response');
        App::container()->instance('response', $response);
    }

    public function tearDown()
    {
        DB::dropAllTables();
    }

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
        $this->assertEquals(1, $this->getRowCount('subscriptions'));
        $this->assertEquals(1, $this->getRowCount('temp_teachers'));
        $this->assertEquals(Subscription::all()->first()->id, TempTeacher::all()->first()->subscription_id);
    }
}
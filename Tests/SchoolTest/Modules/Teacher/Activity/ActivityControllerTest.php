<?php namespace Tests\SchoolTest\Modules\Teacher\Activity;

use Applications\School\Modules\Teacher\Activity\ActivityController;
use Library\Facades\App;
use Library\Facades\DB;
use Library\Facades\Session;
use Tests\SchoolTest\BaseTest;

class ActivityControllerTest extends BaseTest
{
    public function setUp()
    {
        $_POST['_token'] = Session::generateToken();

        $response = $this->getMock('\\Library\\Response');
        App::container()->instance('response', $response);
    }

    public function tearDown()
    {
        DB::dropAllTables();
    }

    public function testCreateWhenValid()
    {
        // Arrange
        $_POST['name'] = 'name1';
        $_POST['defaultRate'] = 50;
        $controller = new ActivityController();

        // Act
        $controller->store();

        // Assert
        $response = App::container()->instance()->make('response');
        $response->expects($this->once())
            ->method('toAction');
    }
}
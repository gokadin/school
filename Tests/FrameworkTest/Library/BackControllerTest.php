<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\Session;
use Tests\FrameworkTest\Applications\TestApplication\Modules\Index\IndexController;
use Tests\FrameworkTest\BaseTest;

class BackControllerTest extends BaseTest
{
    public function testThatTokenValidationWorksCorrectlyWhenValid()
    {
        // Arrange
        $_POST['_token'] = Session::generateToken();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testTokenValidation();

        // Assert
        $this->assertTrue(true, 'if reached this then no exception was thrown');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThatTokenValidationWorksCorrectlyWhenInvalid()
    {
        // Arrange
        $_POST['_token'] = 'other';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testTokenValidation();

        // Assert
        $this->assertTrue(true, 'if reached this then exception was thrown');
    }

    // ... for all validations and for multiple at the same time
}
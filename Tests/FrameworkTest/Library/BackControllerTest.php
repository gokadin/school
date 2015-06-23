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
        $_POST['_method'] = 'POST';
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
        $_POST['_method'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testTokenValidation();

        // Assert
        $this->assertTrue(true, 'if reached this then exception was thrown');
    }

    public function testThatRequestValidationWorksWhenRequiredIsValid()
    {
        // Arrange
        $_POST['one'] = 1;
        $_POST['_method'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testRequestValidation();

        // Assert
        $this->assertFalse(Session::hasErrors());
    }

    public function testThatRequestValidationWorksWhenRequiredIsInvalid()
    {
        // Arrange
        $_POST['_method'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testRequestValidation();

        // Assert
        $this->assertTrue(Session::hasErrors());
    }

    public function testThatRequestValidationWorksWithMultipleValidationsAreAllValid()
    {
        // Arrange
        $_POST['_method'] = 'POST';
        $_POST['one'] = 1;
        $_POST['two'] = 2;
        $controller = new IndexController();

        // Act
        $controller->testMultipleRequestValidation();

        // Assert
        $this->assertFalse(Session::hasErrors());
    }

    public function testThatRequestValidationWorksWithMultipleValidationsWhenNotValid()
    {
        // Arrange
        $_POST['_method'] = 'POST';
        $_POST['one'] = 1;
        $_POST['two'] = 'not a number';
        $controller = new IndexController();

        // Act
        $controller->testMultipleRequestValidation();

        // Assert
        $this->assertTrue(Session::hasErrors());
    }

    public function testThatRequestValidationGeneratesCorrectErrorArray()
    {
        // Arrange
        $_POST['_method'] = 'POST';
        $controller = new IndexController();

        // Act
        $controller->testMultipleRequestValidation();
        $errors = Session::getErrors();

        // Assert
        $this->assertEquals(2, sizeof($errors));
        $this->assertTrue(isset($errors['one']));
        $this->assertTrue(isset($errors['two']));
    }

    // test complex validations... ex: unique:posts ... or ... max:255
}
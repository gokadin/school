<?php

namespace Tests\FrameworkTest\Validation;

use Library\Validation\Validator;
use Library\Facades\Session;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Models\Test;

class ValidatorTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testMakeWorksWithSimpleSingleValidationWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->make(['one' => 1], ['one' => 'required']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithSimpleSingleValidationWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->make(['one' => null], ['one' => 'required']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenAllValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenOneInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->make(
            ['one' => 1, 'two' => 'text', 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMoreDataThanThereAreValidations()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->make(['one' => 20], ['one' => 'min:15']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->make(['one' => 1], ['one' => 'min:15']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenAllValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->make(
            ['one' => 1],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenOneIsInValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->make(
            ['one' => 'text'],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testSingleCustomErrorWorks()
    {
        // Arrange
        $validator = new Validator();

        // Act
        $result = $validator->make(
            ['one' => null],
            ['one' => ['required' => 'custom']]
        );

        // Assert
        $this->assertFalse($result);
        $this->assertTrue(Session::hasErrors());
        $errors = Session::getErrors();
        $this->assertEquals('custom', $errors['one'][0]);
    }

    public function testMultipleCustomErrorsWork()
    {
        // Arrange
        $validator = new Validator();

        // Act
        $result = $validator->make(
            ['one' => 'text', 'two' => null],
            [
                'one' => ['required', 'numeric', 'min:10' => 'customMin'],
                'two' => ['required' => 'customRequired', 'min:10']
            ]
        );

        // Assert
        $this->assertFalse($result);
        $this->assertTrue(Session::hasErrors());
        $errors = Session::getErrors();
        $this->assertEquals(2, sizeof($errors['one']));
        $this->assertEquals('customMin', $errors['one'][1]);
        $this->assertEquals(2, sizeof($errors['two']));
        $this->assertEquals('customRequired', $errors['two'][0]);
    }

    public function testCustomErrorFormatting()
    {
        // Arrange
        $validator = new Validator();

        // Act
        $result = $validator->make(
            ['one' => null],
            ['one' => [
                'required' => '{field} is required',
                'min:10' => '{field} should be higher than {0}'
            ]]
        );

        $this->assertFalse($result);
        $this->assertTrue(Session::hasErrors());
        $errors = Session::getErrors();
        $this->assertEquals('one is required', $errors['one'][0]);
        $this->assertEquals('one should be higher than 10', $errors['one'][1]);
    }

    /* SINGLE METHODS */

    public function testThatRequiredWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->required('test'));
    }

    public function testThatRequiredWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->required(null));
        $this->assertFalse($validator->required(''));
        $this->assertFalse($validator->required('    '));
    }

    public function testThatNumericWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->numeric(3));
    }

    public function testThatNumericWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->numeric(null));
        $this->assertFalse($validator->numeric(''));
        $this->assertFalse($validator->numeric('test'));
    }

    public function testMinWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->min(11, 10));
        $this->assertTrue($validator->min(10, 10));
    }

    public function testMinWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->min(9, 10));
        $this->assertFalse($validator->min('text', 10));
    }

    public function testMaxWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->max(10, 11));
        $this->assertTrue($validator->max(10, 10));
    }

    public function testMaxWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->max(11, 10));
        $this->assertFalse($validator->max('text', 10));
    }

    public function testBetweenWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->between(15, 10, 20));
        $this->assertTrue($validator->between(10, 10, 20));
        $this->assertTrue($validator->between(20, 10, 20));
    }

    public function testBetweenWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->between(9, 10, 20));
        $this->assertFalse($validator->between(21, 10, 20));
        $this->assertFalse($validator->between('text', 10, 20));
    }

    public function testBooleanWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->boolean(true));
        $this->assertTrue($validator->boolean(false));
        $this->assertTrue($validator->boolean(1));
        $this->assertTrue($validator->boolean(0));
        $this->assertTrue($validator->boolean('1'));
        $this->assertTrue($validator->boolean('0'));
    }

    public function testBooleanWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->boolean(2));
        $this->assertFalse($validator->boolean(-1));
        $this->assertFalse($validator->boolean('2'));
        $this->assertFalse($validator->boolean('-1'));
    }

    public function testEmailWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->email('a@b.cc'));
    }

    public function testEmailWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertFalse($validator->email('a@b'));
        $this->assertFalse($validator->email('a@.c'));
        $this->assertFalse($validator->email('a.c'));
        $this->assertFalse($validator->email('@b.c'));
    }

    public function testUniqueWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();

        // Assert
        $this->assertTrue($validator->unique('nonexistant', 'Test', 'col1'));
    }

    public function testUniqueWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();
        Test::create(['col1' => 'str', 'col2' => 1]);

        // Assert
        $this->assertFalse($validator->unique('str', 'Test', 'col1'));
    }

    public function testEqualsFieldWorksWhenValid()
    {
        // Arrange
        $validator = new Validator();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['one'] = 1;

        // Assert
        $this->assertTrue($validator->equalsField(1, 'one'));
    }

    public function testEqualsFieldWorksWhenInvalid()
    {
        // Arrange
        $validator = new Validator();
        $_POST['_method'] = 'POST';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Assert
        $this->assertFalse($validator->equalsField(2, 'one'));
        $this->assertFalse($validator->equalsField(2, 'nonexistant'));
    }
}
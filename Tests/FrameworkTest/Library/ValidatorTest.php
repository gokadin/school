<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\Session;
use Library\Facades\Validator;
use Tests\FrameworkTest\BaseTest;

class ValidatorTest extends BaseTest
{
    public function testMakeWorksWithSimpleSingleValidationWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::make(['one' => 1], ['one' => 'required']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithSimpleSingleValidationWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::make(['one' => null], ['one' => 'required']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenAllValid()
    {
        // Assert
        $this->assertTrue(Validator::make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenOneInvalid()
    {
        // Assert
        $this->assertFalse(Validator::make(
            ['one' => 1, 'two' => 'text', 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMoreDataThanThereAreValidations()
    {
        // Assert
        $this->assertTrue(Validator::make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::make(['one' => 20], ['one' => 'min:15']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::make(['one' => 1], ['one' => 'min:15']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenAllValid()
    {
        // Assert
        $this->assertTrue(Validator::make(
            ['one' => 1],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenOneIsInValid()
    {
        // Assert
        $this->assertFalse(Validator::make(
            ['one' => 'text'],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testSingleCustomErrorWorks()
    {
        // Act
        $result = Validator::make(
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
        // Act
        $result = Validator::make(
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
        // Act
        $result = Validator::make(
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
        // Assert
        $this->assertTrue(Validator::required('test'));
    }

    public function testThatRequiredWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::required(null));
        $this->assertFalse(Validator::required(''));
        $this->assertFalse(Validator::required('    '));
    }

    public function testThatNumericWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::numeric(3));
    }

    public function testThatNumericWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::numeric(null));
        $this->assertFalse(Validator::numeric(''));
        $this->assertFalse(Validator::numeric('test'));
    }

    public function testMinWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::min(11, 10));
        $this->assertTrue(Validator::min(10, 10));
    }

    public function testMinWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::min(9, 10));
        $this->assertFalse(Validator::min('text', 10));
    }

    public function testMaxWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::max(10, 11));
        $this->assertTrue(Validator::max(10, 10));
    }

    public function testMaxWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::max(11, 10));
        $this->assertFalse(Validator::max('text', 10));
    }

    public function testBetweenWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::between(15, 10, 20));
        $this->assertTrue(Validator::between(10, 10, 20));
        $this->assertTrue(Validator::between(20, 10, 20));
    }

    public function testBetweenWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::between(9, 10, 20));
        $this->assertFalse(Validator::between(21, 10, 20));
        $this->assertFalse(Validator::between('text', 10, 20));
    }

    public function testBooleanWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::boolean(true));
        $this->assertTrue(Validator::boolean(false));
        $this->assertTrue(Validator::boolean(1));
        $this->assertTrue(Validator::boolean(0));
        $this->assertTrue(Validator::boolean('1'));
        $this->assertTrue(Validator::boolean('0'));
    }

    public function testBooleanWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::boolean(2));
        $this->assertFalse(Validator::boolean(-1));
        $this->assertFalse(Validator::boolean('2'));
        $this->assertFalse(Validator::boolean('-1'));
    }
}
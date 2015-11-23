<?php

namespace Tests\FrameworkTest\Validation;

use Library\Database\Table;
use Library\Testing\DatabaseTransactions;
use Library\Facades\Session;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\Models\Test;

class ValidatorTest extends BaseTest
{
    use DatabaseTransactions;

    protected $validator;
    protected $database;

    public function setUp()
    {
        parent::setUp();

        $app = $this->createApplication();

        $this->validator = $app->container()->resolveInstance('validator');
        $this->database = $app->container()->resolveInstance('database');
    }

    public function testMakeWorksWithSimpleSingleValidationWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->make(['one' => 1], ['one' => 'required']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithSimpleSingleValidationWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->make(['one' => null], ['one' => 'required']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenAllValid()
    {
        // Assert
        $this->assertTrue($this->validator->make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsWhenOneInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->make(
            ['one' => 1, 'two' => 'text', 'three' => 3],
            ['one' => 'required', 'two' => 'numeric', 'three' => 'required']
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMoreDataThanThereAreValidations()
    {
        // Assert
        $this->assertTrue($this->validator->make(
            ['one' => 1, 'two' => 2, 'three' => 3],
            ['one' => 'required', 'three' => 'required']
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->make(['one' => 20], ['one' => 'min:15']));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithComplexValidationRuleWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->make(['one' => 1], ['one' => 'min:15']));
        $this->assertTrue(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenAllValid()
    {
        // Assert
        $this->assertTrue($this->validator->make(
            ['one' => 1],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertFalse(Session::hasErrors());
    }

    public function testMakeWorksWithMultipleValidationsOnSameFieldWhenOneIsInValid()
    {
        // Assert
        $this->assertFalse($this->validator->make(
            ['one' => 'text'],
            ['one' => ['required', 'numeric']]
        ));
        $this->assertTrue(Session::hasErrors());
    }

    public function testSingleCustomErrorWorks()
    {
        // Act
        $result = $this->validator->make(
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
        $result = $this->validator->make(
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
        $result = $this->validator->make(
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
        $this->assertTrue($this->validator->required('test'));
    }

    public function testThatRequiredWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->required(null));
        $this->assertFalse($this->validator->required(''));
        $this->assertFalse($this->validator->required('    '));
    }

    public function testThatNumericWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->numeric(3));
    }

    public function testThatNumericWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->numeric(null));
        $this->assertFalse($this->validator->numeric(''));
        $this->assertFalse($this->validator->numeric('test'));
    }

    public function testMinWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->min(11, 10));
        $this->assertTrue($this->validator->min(10, 10));
    }

    public function testMinWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->min(9, 10));
        $this->assertFalse($this->validator->min('text', 10));
    }

    public function testMaxWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->max(10, 11));
        $this->assertTrue($this->validator->max(10, 10));
    }

    public function testMaxWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->max(11, 10));
        $this->assertFalse($this->validator->max('text', 10));
    }

    public function testBetweenWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->between(15, 10, 20));
        $this->assertTrue($this->validator->between(10, 10, 20));
        $this->assertTrue($this->validator->between(20, 10, 20));
    }

    public function testBetweenWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->between(9, 10, 20));
        $this->assertFalse($this->validator->between(21, 10, 20));
        $this->assertFalse($this->validator->between('text', 10, 20));
    }

    public function testBooleanWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->boolean(true));
        $this->assertTrue($this->validator->boolean(false));
        $this->assertTrue($this->validator->boolean(1));
        $this->assertTrue($this->validator->boolean(0));
        $this->assertTrue($this->validator->boolean('1'));
        $this->assertTrue($this->validator->boolean('0'));
    }

    public function testBooleanWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->boolean(2));
        $this->assertFalse($this->validator->boolean(-1));
        $this->assertFalse($this->validator->boolean('2'));
        $this->assertFalse($this->validator->boolean('-1'));
    }

    public function testEmailWorksWhenValid()
    {
        // Assert
        $this->assertTrue($this->validator->email('a@b.cc'));
    }

    public function testEmailWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse($this->validator->email('a@b'));
        $this->assertFalse($this->validator->email('a@.c'));
        $this->assertFalse($this->validator->email('a.c'));
        $this->assertFalse($this->validator->email('@b.c'));
    }

    public function testUniqueWorksWhenValid()
    {
        // Arrange
        $table = new Table('Test');
        $table->increments('id');
        $table->integer('col1');
        $this->database->create($table);

        // Assert
        $this->assertTrue($this->validator->unique('nonexistant', 'Test', 'col1'));

        // Arrange
        $this->database->drop('Test');
    }

    public function testUniqueWorksWhenInvalid()
    {
        // Arrange
        $table = new Table('Test');
        $table->increments('id');
        $table->integer('col1');
        $this->database->create($table);
        $this->database->table('Test')->insert(['col1' => 'sr']);

        // Assert
        $this->assertFalse($this->validator->unique('str', 'Test', 'col1'));

        // Arrange
        $this->database->drop('Test');
    }

    public function testEqualsFieldWorksWhenValid()
    {
        // Arrange
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['one'] = 1;

        // Assert
        $this->assertTrue($this->validator->equalsField(1, 'one'));
    }

    public function testEqualsFieldWorksWhenInvalid()
    {
        // Arrange
        $_POST['_method'] = 'POST';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // Assert
        $this->assertFalse($this->validator->equalsField(2, 'one'));
        $this->assertFalse($this->validator->equalsField(2, 'nonexistant'));
    }
}
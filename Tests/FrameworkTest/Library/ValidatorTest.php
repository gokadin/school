<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\Validator;
use Tests\FrameworkTest\BaseTest;

class ValidatorTest extends BaseTest
{
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

    public function testThatNumberWorksWhenValid()
    {
        // Assert
        $this->assertTrue(Validator::number(3));
    }

    public function testThatNumberWorksWhenInvalid()
    {
        // Assert
        $this->assertFalse(Validator::number(null));
        $this->assertFalse(Validator::number(''));
        $this->assertFalse(Validator::number('test'));
    }
}
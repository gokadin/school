<?php namespace Tests\FrameworkTests\Library;

use Tests\FrameworkTests\BaseTest;
use Library\Facades\Page;

class PageTest extends BaseTest
{
    public function testThatICanAddAVariableToThePage()
    {
        // Arrange
        $var = 'testVar';
        $value = 'testValue';

        // Act
        Page::add($var, $value);

        // Assert
        $this->assertTrue(Page::exists($var));
        $this->assertEquals($value, Page::get($var));
    }

    public function testThatICanAddAnArrayToThePage()
    {
        // Arrange
        $array = ['var1' => 'value1', 'var2' => 'value2', 'novarname'];

        // Act
        Page::add($array);

        // Assert
        $this->assertTrue(Page::exists('var1'));
        $this->assertTrue(Page::exists('var2'));
        $this->assertTrue(Page::exists(0));
        $this->assertEquals('value1', Page::get('var1'));
        $this->assertEquals('value2', Page::get('var2'));
        $this->assertEquals('novarname', Page::get(0));
    }
}
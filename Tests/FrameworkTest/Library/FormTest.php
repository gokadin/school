<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\Form;
use Tests\FrameworkTest\BaseTest;

class FormTest extends BaseTest
{
    public function testThatSimpleLabelIsCorrectlyGenerated()
    {
        // Arrange
        $expected = '<label for="test">Hello:</label>';

        // Act
        $result = Form::label('test', 'Hello:');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatLabelIsCorrectlyGeneratedWithOptions()
    {
        // Arrange
        $expected = '<label for="test" class="class1 class2" id="id1">Hello:</label>';

        // Act
        $result = Form::label('test', 'Hello:', ['class' => 'class1 class2', 'id' => 'id1']);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatSimpleTextIsCorrectlyGenerated()
    {
        // Arrange
        $expected = '<input type="text" name="aname" id="aname" />';

        // Act
        $result = Form::text('aname');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatTextIsCorrectlyGeneratedWithDefaultValue()
    {
        // Arrange
        $expected = '<input type="text" name="aname" id="aname" value="adefaultvalue" />';

        // Act
        $result = Form::text('aname', 'adefaultvalue');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatTextIsCorrectlyGeneratedWithDefaultValueAndOptions()
    {
        // Arrange
        $expected = '<input type="text" name="aname" id="aname" value="adefaultvalue" class="aclass" />';

        // Act
        $result = Form::text('aname', 'adefaultvalue', ['class' => 'aclass']);

        // Assert
        $this->assertEquals($expected, $result);
    }
}
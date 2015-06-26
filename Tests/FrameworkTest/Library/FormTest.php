<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\Form;
use Tests\FrameworkTest\BaseTest;

class FormTest extends BaseTest
{
    public function testThatSimpleFormIsCorrectlyGenerated()
    {
        // Arrange
        $expected = '<form action="" method="POST" id="aname"><input type="hidden" name="_token" value="'. \Library\Facades\Session::generateToken() .'" />';

        // Act
        $result = Form::open('aname', '');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatSimpleFormIsCorrectlyGeneratedWhenMethodIsNotGetOrPost()
    {
        // Arrange
        $expected = '<form action="" method="POST" id="aname">'
            .'<input type="hidden" name="_method" value="DELETE" />'
            .'<input type="hidden" name="_token" value="'. \Library\Facades\Session::generateToken() .'" />';

        // Act
        $result = Form::open('aname', '', 'DELETE');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatSimpleFormIsCorrectlyGeneratedWhenIdIsSpecified()
    {
        // Arrange
        $expected = '<form action="" method="POST" id="customId">'
            .'<input type="hidden" name="_token" value="'. \Library\Facades\Session::generateToken() .'" />';

        // Act
        $result = Form::open('aname', '', 'POST', ['id' => 'customId']);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatSimpleFormIsCorrectlyGeneratedWhenTokenIsNotWanted()
    {
        // Arrange
        $expected = '<form action="" method="POST" id="aname">';

        // Act
        $result = Form::open('aname', '', 'POST', null, false);

        // Assert
        $this->assertEquals($expected, $result);
    }

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
        $expected = '<input type="text" name="aname" value="adefaultvalue" id="aname" />';

        // Act
        $result = Form::text('aname', 'adefaultvalue');

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function testThatTextIsCorrectlyGeneratedWithDefaultValueAndOptions()
    {
        // Arrange
        $expected = '<input type="text" name="aname" value="adefaultvalue" class="aclass" id="aname" />';

        // Act
        $result = Form::text('aname', 'adefaultvalue', ['class' => 'aclass']);

        // Assert
        $this->assertEquals($expected, $result);
    }
}
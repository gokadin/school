<?php namespace Tests\FrameworkTests\Database;

use Library\Database\Blueprint;
use Library\Database\Column;
use Tests\FrameworkTests\BaseTest;

class ExampleTests extends BaseTest
{
    public function testIfIReturnColumnInstanceThenModifyingItShouldChangeTheInstanceInBlueprintArray()
    {
        // Arrange
        $blueprint = new Blueprint('test');

        // Act
        $blueprint->integer('col1')->unique();
        $columns = $blueprint->columns();

        // Assert
        $this->assertTrue($columns[0]->isUnique());
    }

    public function testIfColumnsDefaultGetsCalledProperlyWithDifferentNumberOfArgs()
    {
        // Arrange
        $column = new Column('col1', 'string');

        // Act
        $column->default('test');
        $column->default('wrong', 'wrong');
        $column->default();

        // Assert
        $this->assertEquals('test', $column->getDefault());
    }
}
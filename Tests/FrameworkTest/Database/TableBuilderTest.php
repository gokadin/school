<?php namespace Tests\FrameworkTest\Database;

use Library\Database\Column;
use Library\Database\Table;
use Tests\FrameworkTest\BaseTest;

class TableBuilderTest extends BaseTest
{
    public function testIfIReturnColumnInstanceThenModifyingItShouldChangeTheInstanceInTableArray()
    {
        // Arrange
        $t = new Table('test');

        // Act
        $t->integer('col1')->unique();
        $columns = $t->columns();

        // Assert
        $this->assertTrue($columns['col1']->isUnique());
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

    public function testThatTimestampColumnsAreCorrectlyCreated()
    {
        // Arrange
        $t = new Table('testModel');

        // Act
        $t->timestamps();

        // Assert
        $this->assertEquals(2, sizeof($t->columns()));
        $this->assertTrue($t->hasColumn(Table::UPDATED_AT));
        $this->assertTrue($t->hasColumn(Table::CREATED_AT));
    }

    public function testThatTimestampColumnsAreNotRequired()
    {
        // Arrange
        $t = new Table('testModel');

        // Act
        $t->timestamps();
        $columns = $t->columns();

        // Assert
        $this->assertFalse($columns[Table::UPDATED_AT]->isRequired());
        $this->assertFalse($columns[Table::CREATED_AT]->isRequired());
    }

    public function testThatNonNullableAndNonDefaultRegularColumnsAreRequired()
    {
        // Arrange
        $column = new Column('testName', 'string');

        // Assert
        $this->assertFalse($column->isNullable());
        $this->assertFalse($column->isDefault());
        $this->assertTrue($column->isRequired());
    }

    public function testThatDefaultColumnsAreNotRequired()
    {
        // Arrange
        $column = new Column('testName', 'string');

        // Act
        $this->assertTrue($column->isRequired());
        $column->default(1);

        // Assert
        $this->assertTrue($column->isDefault());
        $this->assertFalse($column->isRequired());
    }

    public function testThatNullableColumnsAreNotRequired()
    {
        // Arrange
        $column = new Column('testName', 'string');

        // Act
        $this->assertTrue($column->isRequired());
        $column->nullable();

        // Assert
        $this->assertFalse($column->isRequired());
    }
}
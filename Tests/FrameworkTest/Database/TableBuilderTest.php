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

    /**
     * @depends testThatTimestampColumnsAreCorrectlyCreated
     */
    public function testThatTimestampColumnsAreNotRequired()
    {
        // Arrange
        $t = new Table('testModel');

        // Act
        $t->timestamps();
        $columns = $t->columns();

        // Assert
        $this->assertFalse($columns[0]->isRequired());
        $this->assertFalse($columns[1]->isRequired());
    }

    public function testThatMetaColumnsAreCorrectlyCreated()
    {
        // Arrange
        $t = new Table('testModel');

        // Act
        $t->meta();

        // Assert
        $this->assertTrue($t->isMeta());
        $this->assertEquals(2, sizeof($t->columns()));
        $this->assertTrue($t->hasColumn(Table::META_TYPE));
        $this->assertTrue($t->hasColumn(Table::META_ID));
    }

    /**
     * @depends testThatMetaColumnsAreCorrectlyCreated
     */
    public function testThatMetaColumnsAreNotRequired()
    {
        // Arrange
        $t = new Table('testModel');

        // Act
        $t->meta();
        $columns = $t->columns();

        // Assert
        $this->assertFalse($columns[0]->isRequired());
        $this->assertFalse($columns[1]->isRequired());
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
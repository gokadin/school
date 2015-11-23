<?php

namespace FrameworkTest\Database\Schema;

use Library\Database\Database;
use Library\Database\Schema;
use Library\Database\Table;
use Tests\FrameworkTest\BaseTest;

class SchemaTest extends BaseTest
{
    public function testAdd()
    {
        // Arrange
        $schema = new Schema(new Database([
            'driver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ]
        ]));

        // Act
        $schema->add(new Table('test1'));
        $schema->add(new Table('test2'));

        // Assert
        $this->assertEquals(2, sizeof($schema->tables()));
    }
}
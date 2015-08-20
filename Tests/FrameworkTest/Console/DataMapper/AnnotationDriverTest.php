<?php

namespace FrameworkTest\Console\DataMapper;

use Library\Console\Modules\DataMapper\AnnotationDriver;
use Library\Database\Database;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Console\DataMapper\EmptyEntity;
use Tests\FrameworkTest\TestData\Console\DataMapper\SimpleEntity;

class AnnotationDriverTest extends BaseTest
{
    protected $databaseSettings;

    public function setUp()
    {
        parent::setUp();

        $this->databaseSettings = [
            'driver' => 'redis',
            'redis' => [
                'database' => 0
            ]
        ];
    }

    public function testBuildForNoClasses()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), []);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertEquals(0, sizeof($schema->tables()));
    }

    public function testBuildForOneEmptyClass()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            EmptyEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('EmptyEntity'));
    }

    public function testBuildWenEntityNameIsProvided()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity'));
    }

    public function testBuildForIdColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('id'));
        $this->assertTrue($schema->table('simpleEntity')->column('id')->isPrimaryKey());
    }

    public function testBuildForIntegerColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('one'));
        $this->assertNotNull($schema->table('simpleEntity')->column('customName'));
        $this->assertEquals(12, $schema->table('simpleEntity')->column('customName')->getSize());
    }

    public function testBuildForStringColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('str1'));
        $this->assertNotNull($schema->table('simpleEntity')->column('customName2'));
        $this->assertEquals(25, $schema->table('simpleEntity')->column('customName2')->getSize());
    }

    public function testBuildForTimestampsTrait()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('created_at'));
        $this->assertNotNull($schema->table('simpleEntity')->column('updated_at'));
        $this->assertTrue($schema->table('simpleEntity')->hasTimestamps());
    }

    public function testBuildForTextColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('text1'));
    }

    public function testBuildForBooleanColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('bool1'));
    }

    public function testBuildForDecimalColumn()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertNotNull($schema->table('simpleEntity')->column('decimal1'));
        $this->assertEquals(3, $schema->table('simpleEntity')->column('decimal2')->getPrecision());
    }

    public function testBuildForIndexedColumns()
    {
        // Arrange
        $driver = new AnnotationDriver(new Database($this->databaseSettings), [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();

        // Assert
        $this->assertTrue($schema->table('simpleEntity')->column('one')->hasIndex());
    }
}
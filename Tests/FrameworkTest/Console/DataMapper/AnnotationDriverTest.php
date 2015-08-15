<?php

namespace FrameworkTest\Console\DataMapper;

use Library\Console\Modules\DataMapper\AnnotationDriver;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Console\DataMapper\EmptyEntity;

class AnnotationDriverTest extends BaseTest
{
    public function testBuildForNoClasses()
    {
        // Arrange
        $driver = new AnnotationDriver([]);

        // Act
        $result = $driver->build();

        // Assert
        $this->assertEquals([], $result);
    }

    public function testBuildForOneEmptyClass()
    {
        // Arrange
        $driver = new AnnotationDriver([
            EmptyEntity::class
        ]);
        $expected = [
            EmptyEntity::class => [
                'name' => 'EmptyEntity',
                'columns' => [],
                'relationships' => []
            ]
        ];

        // Act
        $result = $driver->build();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
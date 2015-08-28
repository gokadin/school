<?php

namespace FrameworkTest\Database\DataMapper;

use Library\Database\Database;
use Library\Database\DataMapper\DataMapper;
use Tests\FrameworkTest\BaseTest;

class DataMapperTest extends BaseTest
{
    protected $dm;
    protected $database;

    public function setUp()
    {
        parent::setUp();

        $databaseSettings = [
            'driver' => 'mysql',
            'redis' => [// change...................
                'database' => 1
            ]
        ];

        $this->database = new Database($databaseSettings);
        $this->dm = new DataMapper($this->database);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->database->dropAll();
    }

    public function testPersistWhenInsertingANewObject()
    {
        // Arrange
        $this->dm->persist()

        // Act

        // Assert
    }

    public function testFind()
    {
        // Arrange

        // Act

        // Assert
        $this->assertTrue(false);
    }
}
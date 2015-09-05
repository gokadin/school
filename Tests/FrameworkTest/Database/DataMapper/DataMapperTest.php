<?php

namespace FrameworkTest\Database\DataMapper;

use FrameworkTest\TestData\Database\DataMapper\SimpleEntity;
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
            'mysql' => [
                'host' => 'localhost',
                'database' => 'FrameworkTest',
                'username' => 'root',
                'password' => 'f10ygs87'
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
        // Act
        $this->dm->persist(new Entity(1, 2));
        $results = $this->database->table('simpleEntity')->get();

        // Assert
        $this->assertNotNull($results);
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(1, $results[0]->one());
        $this->assertEquals(2, $results[0]->two());
    }

    public function testFind()
    {
        // Arrange

        // Act

        // Assert
        $this->assertTrue(false);
    }
}
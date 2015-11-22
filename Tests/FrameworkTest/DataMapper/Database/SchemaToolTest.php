<?php

namespace FrameworkTest\DataMapper\Database;

use Library\DataMapper\Database\SchemaTool;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;
use PDO;

class SchemaToolTest extends BaseTest
{
    protected $schemaTool;
    protected $dao;

    public function setUp()
    {
        parent::setUp();

        $config = [
            'mappingDriver' => 'annotation',

            'databaseDriver' => 'mysql',

            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ],

            'classes' => [
                SimpleEntity::class
            ]
        ];

        $this->schemaTool = new SchemaTool($config);

        $this->dao = new PDO('mysql:host='.$config['mysql']['host'].';dbname='.$config['mysql']['database'],
            $config['mysql']['username'],
            $config['mysql']['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->schemaTool->drop();
    }

    public function testCreate()
    {
        // Act
        $successes = $this->schemaTool->create();

        // Assert
        $results = $this->dao->query('SHOW TABLES LIKE \'simpleEntity\'');
        $this->assertGreaterThan(0, $results->rowCount());
        $this->assertTrue(isset($successes['simpleEntity']));
        $this->assertTrue($successes['simpleEntity']);
    }

    public function testDrop()
    {
        // Arrange
        $this->schemaTool->create();

        // Act
        $this->schemaTool->drop();

        // Assert
        $results = $this->dao->query('SHOW TABLES LIKE \'simpleEntity\'');
        $this->assertEquals(0, $results->rowCount());
    }
}

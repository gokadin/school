<?php

namespace FrameworkTest\DataMapper;

use Library\DataMapper\DataMapper;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Console\DataMapper\SimpleEntity;
use Library\DataMapper\Database\SchemaTool;
use PDO;

class DataMapperTest extends BaseTest
{
    protected $schemaTool;
    protected $dao;
    protected $dm;

    protected function setUpSimpleEntity()
    {
        date_default_timezone_set('America/Montreal');

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
        $this->schemaTool->create();

        $this->dao = new PDO('mysql:host='.$config['mysql']['host'].';dbname='.$config['mysql']['database'],
            $config['mysql']['username'],
            $config['mysql']['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->dm = new DataMapper($config);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->schemaTool->drop();
    }

    public function testPersistWhenInserting()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(1, $results[0]['one']);
    }
}
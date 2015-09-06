<?php

namespace FrameworkTest\Database\DataMapper;

use Library\Console\Modules\DataMapper\AnnotationDriver;
use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
use Library\Database\Database;
use Library\Database\DataMapper\DataMapper;
use Predis\Client;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Console\DataMapper\SimpleEntity;

class DataMapperTest extends BaseTest
{
    protected $dm;
    protected $database;

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('America/Montreal');

        $databaseSettings = [
            'driver' => 'mysql',
            'mysql' => [
                'host' => 'localhost',
                'database' => 'FrameworkTest',
                'username' => 'root',
                'password' => 'f10ygs87'
            ]
        ];

        $dataMapperSettings = [
            'config' => [
                'databaseDriver' => 'mysql',
                'cacheDriver' => 'redis',
                'redisDatabase' => 2,
                'mappingDriver' => 'annotation'
            ],

            'classes' => [
                SimpleEntity::class
            ]
        ];

        $this->database = new Database($databaseSettings);
        $this->dm = new DataMapper($this->database, $dataMapperSettings);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->database->dropAll();

        $redis = new Client(['database' => 2]);
        $redis->flushdb();
    }

    public function loadSimpleEntity()
    {
        $annotationDriver = new AnnotationDriver($this->database, [
            SimpleEntity::class
        ]);

        $schema = $annotationDriver->build();

        $schema->createAll();

        $cache = new DataMapperRedisCacheDriver(2);
        $cache->loadSchema($schema);
    }

    public function testPersistWhenInsertingANewObject()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $result = $this->database->table('simpleEntity')->select()[0];
        $this->assertEquals(1, $result['one']);
        $this->assertEquals(2, $result['customName']);
        $this->assertEquals('one', $result['str1']);
        $this->assertEquals('two', $result['customName2']);
    }
}
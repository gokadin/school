<?php

namespace FrameworkTest\Database\DataMapper;

use Library\Console\Modules\DataMapper\AnnotationDriver;
use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
use Library\Database\Database;
use Library\Database\DataMapper\DataMapper;
use Predis\Client;
use Symfony\Component\Yaml\Exception\RuntimeException;
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

    public function testPersistCorrectlySetsTheIdOnAnInsertedObject()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');

        // Assert
        $this->assertNull($simpleEntity->getId());

        // Act
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $this->assertTrue(is_numeric($simpleEntity->getId()));
    }

    public function testPersistWhenInsertingMultipleNewObject()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity1 = new SimpleEntity(1, 2, 'one', 'two');
        $simpleEntity2 = new SimpleEntity(10, 11, 'ten', 'eleven');

        // Act
        $this->dm->persist($simpleEntity1);
        $this->dm->persist($simpleEntity2);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $this->assertEquals(2, sizeof($results));
    }

    public function testPersistWhenUpdatingAnObject()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $entity = $results[0];
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(1, $entity['one']);
        $this->assertEquals(2, $entity['customName']);
        $this->assertEquals('one', $entity['str1']);
        $this->assertEquals('two', $entity['customName2']);

        // Act
        $id = $entity['id'];
        $simpleEntity->setId($id);
        $simpleEntity->setOne(10);
        $simpleEntity->setStr1('ten');
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $entity = $results[0];
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(10, $entity['one']);
        $this->assertEquals(2, $entity['customName']);
        $this->assertEquals('ten', $entity['str1']);
        $this->assertEquals('two', $entity['customName2']);
    }

    public function testFind()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Act
        $entity = $this->dm->find(SimpleEntity::class, $simpleEntity->getId());

        // Assert
        $this->assertNotNull($entity);
        $this->assertEquals($simpleEntity->getId(), $entity->getId());
        $this->assertEquals($simpleEntity->getOne(), $entity->getOne());
        $this->assertEquals($simpleEntity->getTwo(), $entity->getTwo());
        $this->assertEquals($simpleEntity->getStr1(), $entity->getStr1());
        $this->assertEquals($simpleEntity->getStr2(), $entity->getStr2());
    }

    public function testFindWhenThereIsNoRecord()
    {
        // Arrange
        $this->loadSimpleEntity();

        // Act
        $entity = $this->dm->find(SimpleEntity::class, 1234);

        // Assert
        $this->assertNull($entity);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOrFailWhenItShouldFail()
    {
        // Arrange
        $this->loadSimpleEntity();

        // Act
        $this->dm->findOrFail(SimpleEntity::class, 1234);
    }

    public function testDeleteFromClassName()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete(SimpleEntity::class, $simpleEntity->getId());
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }

    public function testDeleteFromObject()
    {
        // Arrange
        $this->loadSimpleEntity();
        $simpleEntity = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($simpleEntity);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete($simpleEntity);
        $this->dm->flush();

        // Assert
        $results = $this->database->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }
}
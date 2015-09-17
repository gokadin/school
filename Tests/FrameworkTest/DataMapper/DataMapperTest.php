<?php

namespace FrameworkTest\DataMapper;

use Library\DataMapper\DataMapper;
use Symfony\Component\Yaml\Exception\RuntimeException;
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

    public function testPersistWhenUpdating()
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

        // Act
        $se->setOne(10);
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));
        $this->assertEquals(10, $results[0]['one']);
    }

    public function testDeleteWhenPassingClassNameAndId()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete(get_class($se), $se->getId());

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }

    public function testDeleteWhenPassingObject()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $this->dm->persist($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(1, sizeof($results));

        // Act
        $this->dm->delete($se);

        // Assert
        $results = $this->dm->queryBuilder()->table('simpleEntity')->select();
        $this->assertEquals(0, sizeof($results));
    }

    public function testFind()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se);

        // Act
        $result = $this->dm->find(SimpleEntity::class, $se->getId());

        // Assert
        $this->assertTrue($result instanceof SimpleEntity);
        $this->assertEquals($se->getId(), $result->getId());
    }

    public function testFindWhenNotFound()
    {
        // Arrange
        $this->setUpSimpleEntity();

        // Act
        $result = $this->dm->find(SimpleEntity::class, 1);

        // Assert
        $this->assertNull($result);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOrFailWhenNotFound()
    {
        // Arrange
        $this->setUpSimpleEntity();

        // Act
        $result = $this->dm->findOrFail(SimpleEntity::class, 1);

        // Assert
        $this->assertNull($result);
    }

    public function testFindAll()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $this->dm->persist(new SimpleEntity(1, 2, 'one', 'two'));
        $this->dm->persist(new SimpleEntity(11, 12, 'one2', 'two2'));
        $this->dm->persist(new SimpleEntity(21, 22, 'one3', 'two3'));

        // Act
        $entities = $this->dm->findAll(SimpleEntity::class);

        // Assert
        $this->assertEquals(3, sizeof($entities));
        $this->assertTrue($entities[0] instanceof SimpleEntity);
    }

    public function testFindBy()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(11, 12, 'one2', 'two2');
        $this->dm->persist($se2);

        // Act
        $entities = $this->dm->findBy(SimpleEntity::class, ['one' => 11]);

        // Assert
        $this->assertEquals(1, sizeof($entities));
        $this->assertEquals(11, $entities[0]->getOne());
    }

    public function testFindByWithMultipleEntitiesFound()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(1, 12, 'one', 'two2');
        $this->dm->persist($se2);

        // Act
        $entities = $this->dm->findBy(SimpleEntity::class, ['one' => 1, 'str1' => 'one']);

        // Assert
        $this->assertEquals(2, sizeof($entities));
    }

    public function testFindOneBy()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $se2 = new SimpleEntity(1, 12, 'one', 'two2');
        $this->dm->persist($se2);

        // Act
        $entities = $this->dm->findOneBy(SimpleEntity::class, ['one' => 1, 'str1' => 'one']);

        // Assert
        $this->assertEquals(1, sizeof($entities));
    }
}
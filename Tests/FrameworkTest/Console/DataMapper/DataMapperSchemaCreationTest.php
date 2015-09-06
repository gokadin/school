<?php

namespace FrameworkTest\Console\DataMapper;

use Library\Database\Database;
use Library\Console\Modules\DataMapper\AnnotationDriver;
use Tests\FrameworkTest\TestData\Console\DataMapper\SimpleEntity;
use Tests\FrameworkTest\BaseTest;
use PDO;

class DataMapperSchemaCreationTest extends BaseTest
{
    protected $databaseSettings;
    protected $database;

    public function tearDown()
    {
        parent::tearDown();

        $this->database->dropAll();
    }

    public function setUpMySqlDatabase()
    {
        $this->databaseSettings = [
            'driver' => 'mysql',
            'mysql' => [
                'host' => 'localhost',
                'database' => 'FrameworkTest',
                'username' => 'root',
                'password' => 'f10ygs87'
            ]
        ];

        $this->database = new Database($this->databaseSettings);
    }

    public function setUpRedisDatabase()
    {
        $this->databaseSettings = [
            'driver' => 'redis',
            'redis' => [
                'database' => '12' // WHAT?? change
            ]
        ];

        $this->database = new Database($this->databaseSettings);
    }

    public function getDao()
    {
        $settings = $this->databaseSettings['mysql'];
        $dao = new PDO('mysql:host='.$settings['host'].';dbname='.$settings['database'],
            $settings['username'],
            $settings['password']);

        $dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dao;
    }

    public function tableExists($dao, $tableName)
    {
        return $dao->query('SHOW TABLES LIKE \''.$tableName.'\'')->rowCount() > 0;
    }

    public function getTableDescription($dao, $tableName)
    {
        $q = $dao->prepare('DESCRIBE '.$tableName);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_COLUMN);
    }

    public function testSchemaIsCorrectlyCreatedForMySql()
    {
        // Arrange
        $this->setUpMySqlDatabase();
        $driver = new AnnotationDriver($this->database, [
            SimpleEntity::class
        ]);

        // Act
        $schema = $driver->build();
        $schema->createAll();

        // Assert
        $dao = $this->getDao();
        $this->assertTrue($this->tableExists($dao, 'simpleEntity'));
        $columns = $this->getTableDescription($dao, 'simpleEntity');
        var_dump($columns);
        $this->assertEquals(11, sizeof($columns));
    }
}
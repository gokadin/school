<?php

namespace Tests\FrameworkTest\DataMapper;

use Library\DataMapper\DataMapper;
use Library\DataMapper\EntityCollection;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\DataMapper\Address;
use Tests\FrameworkTest\TestData\DataMapper\AddressTwo;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;
use Library\DataMapper\Database\SchemaTool;
use PDO;
use Tests\FrameworkTest\TestData\DataMapper\Teacher;
use Tests\FrameworkTest\TestData\DataMapper\Student;

abstract class DataMapperBaseTest extends BaseTest
{
    protected $schemaTool;

    /**
     * @var PDO
     */
    protected $dao;
    protected $dm;
    protected $classes;

    protected function setUpBase()
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

            'classes' => $this->classes
        ];

        $this->schemaTool = new SchemaTool($config);
        $this->schemaTool->create();

        $this->dao = new PDO('mysql:host='.$config['mysql']['host'].';dbname='.$config['mysql']['database'],
            $config['mysql']['username'],
            $config['mysql']['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->dm = new DataMapper($config);
    }

    protected function setUpSimpleEntity()
    {
        $this->classes = [
            SimpleEntity::class
        ];

        $this->setUpBase();
    }

    public function setUpAssociations()
    {
        $this->classes = [
            Teacher::class,
            Student::class,
            Address::class,
            AddressTwo::class
        ];

        $this->setUpBase();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->schemaTool->drop();

        $this->dao = null;
    }
}
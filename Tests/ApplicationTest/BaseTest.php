<?php

namespace Tests\ApplicationTest;

use Library\DataMapper\Database\SchemaTool;
use Library\DataMapper\DataMapper;
use Library\Database\Database;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    protected $app;
    protected $database;
    protected $dm;

    public function setUp()
    {
        parent::setUp();

        $this->app = $this->createApplication();
    }

    public function setUpDatabase()
    {
        $this->database = new Database([
            'driver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ]
        ]);
    }

    public function setUpDatamapper(array $classes)
    {
        $dmConfig = [
            'mappingDriver' => 'annotation',
            'databaseDriver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ],
            'classes' => $classes
        ];

        $this->dm = new DataMapper($dmConfig);

        $schemaTool = new SchemaTool($dmConfig);
        $schemaTool->create();
    }
}
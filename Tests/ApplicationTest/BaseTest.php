<?php

namespace Tests\ApplicationTest;

use Library\Facades\ModelFactory as Factory;
use Library\Facades\Sentry;
use Models\School;
use Models\Teacher;
use Predis\Client;
use Library\Console\Modules\DataMapper\AnnotationDriver;
use Library\Console\Modules\DataMapper\DataMapperRedisCacheDriver;
use Library\Database\DataMapper\DataMapper;
use Library\Database\Database;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    protected $db;
    protected $dm;

    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function setUpDatabase()
    {
        $dbSettings = [
            'driver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ]
        ];

        $this->db = new Database($dbSettings);
    }

    public function setUpDatamapper(array $classes)
    {
        $dmSettings = [
            'config' => [
                'cacheDriver' => 'redis',
                'redisDatabase' => 14,
                'mappingDriver' => 'annotation'
            ],
            'classes' => $classes
        ];

        $mappingDriver = new AnnotationDriver($this->db, $dmSettings['classes']);
        $schema = $mappingDriver->build();
        $schema->createAll();
        $cacheDriver = new DataMapperRedisCacheDriver(14);
        $cacheDriver->loadSchema($schema);

        $this->dm = new DataMapper($this->db, $dmSettings);
    }

    protected function flushRedis($db)
    {
        $redis = new Client();
        $redis->select($db);
        $redis->flushdb();
    }

    public function authenticateTeacher()
    {
        $school = Factory::of(School::class)->create();
        $teacher = Factory::of(Teacher::class)->create([
            'school_id' => $school->id
        ]);
        Sentry::login($teacher->id, 'Teacher');
    }
}
<?php

namespace Tests\ApplicationTest;

use Library\Facades\DB;
use Library\Facades\ModelFactory as Factory;
use Library\Facades\Sentry;
use Library\Testing\DatabaseTransactions;
use Models\Teacher;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    use DatabaseTransactions;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        DB::dropAllTables();
    }

    public function authenticateTeacher()
    {
        $teacher = Factory::of(Teacher::class)->create();
        Sentry::login($teacher->id, 'Teacher');
    }
}
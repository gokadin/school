<?php

namespace Tests\ApplicationTest;

use Library\Facades\DB;
use Library\Facades\ModelFactory as Factory;
use Library\Facades\Sentry;
use Library\Testing\DatabaseTransactions;
use Models\School;
use Models\Teacher;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function tearDown()
    {
        parent::tearDown();

        DB::dropAllTables();
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
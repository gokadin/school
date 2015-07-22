<?php

namespace Tests\ApplicationTest;

use Library\Facades\DB;
use Library\Testing\DatabaseTransactions;
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

        $this->beginDatabaseTransaction();
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
}
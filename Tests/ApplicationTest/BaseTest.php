<?php

namespace Tests\ApplicationTest;

use Library\Facades\DB;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
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
}
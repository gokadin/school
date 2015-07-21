<?php

namespace Tests\FrameworkTest;

use Library\Facades\DB;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {
        \Library\Facades\DB::dropAllTables();
    }

    public function setUp()
    {
        parent::setUp();

        putenv('APP_ENV=framework_testing');

        $this->createApplication();

        \Library\Facades\DB::beginTransaction();
    }

    public function tearDown()
    {
        \Library\Facades\DB::rollBack();
    }

    public function getRowCount($tableName)
    {
        return DB::query('SELECT COUNT(*) FROM '.$tableName)->fetchColumn();
    }
}
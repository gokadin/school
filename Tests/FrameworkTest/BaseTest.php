<?php

namespace Tests\FrameworkTest;

use Library\Facades\DB;
use Library\Testing\DatabaseTransactions;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        putenv('APP_ENV=framework_testing');

        $this->createApplication();

        $this->beginDatabaseTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();

        DB::dropAllTables();
    }

    public function getRowCount($tableName)
    {
        return DB::query('SELECT COUNT(*) FROM '.$tableName)->fetchColumn();
    }
}
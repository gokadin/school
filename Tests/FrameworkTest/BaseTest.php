<?php

namespace Tests\FrameworkTest;

use Library\Facades\DB;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        putenv('APP_ENV=framework_testing');

        require __DIR__.'/../../Bootstrap/env.php';
    }

    public function getRowCount($tableName)
    {
        return DB::query('SELECT COUNT(*) FROM '.$tableName)->fetchColumn();
    }
}
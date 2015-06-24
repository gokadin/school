<?php namespace Tests\FrontendTest;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Library\Config::temporary('testing', 'true');
        new FrontendApplication('Frontend');
    }

    public static function tearDownAfterClass()
    {
        \Library\Facades\DB::dropAllTables();
    }

    public function setUp()
    {
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
<?php namespace Tests\FrameworkTest;

use Library\Facades\DB;
use Library\Facades\Facade;
use PHPUnit_Framework_TestCase;
use Library\Application;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Library\Config::temporary('frameworkTesting', 'true');
    }

    public static function tearDownAfterClass()
    {
        \Library\Facades\DB::dropAllTables();
    }

    public function setUp()
    {
        Facade::resetResolvedInstances();
        new Application('test');

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
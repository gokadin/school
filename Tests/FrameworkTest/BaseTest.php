<?php namespace Tests\FrameworkTest;

use Library\Facades\DB;
use PHPUnit_Framework_TestCase;
use Library\Application;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        \Library\Config::temporary('testing', 'true');
        new Application('test');
    }

    public static function tearDownAfterClass()
    {
        \Library\Facades\DB::dropAllTables();
    }

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function getRowCount($tableName)
    {
        return DB::query('SELECT COUNT(*) FROM '.$tableName)->fetchColumn();
    }
}
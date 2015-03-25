<?php namespace Tests\FrameworkTest;

use Library\Facades\DB;
use PHPUnit_Framework_TestCase;
use Library\Application;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected static $app; // do I need this? app facade?

    public static function setUpBeforeClass()
    {
        \Library\Config::temporary('testing', 'true');
        self::$app = new Application('test');
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
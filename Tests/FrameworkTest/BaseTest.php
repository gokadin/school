<?php namespace Tests\FrameworkTest;

use PHPUnit_Framework_TestCase;
use Library\Application;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected $app; // do I need this? app facade?

    public function setUp()
    {
        \Library\Config::temporary('testing', 'true');
        $this->app = new Application('test');
    }

    public function tearDown()
    {
        \Library\Facades\DB::dropAllTables();
    }
}
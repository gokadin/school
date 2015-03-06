<?php namespace Tests;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;
use Applications\School\SchoolApplication;
use Applications\Backend\BackendApplication;

//require 'Applications/Frontend/FrontendApplication.class.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    protected $appName;
    protected $app;

    public function setUp()
    {
        @session_start();
        $app = new FrontendApplication();
        //$app->run();
    }

    public function testx()
    {
        $this->assertEquals(0, 0);
    }
}
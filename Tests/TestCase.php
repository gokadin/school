<?php namespace Tests;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;
use Applications\School\SchoolApplication;
use Applications\Backend\BackendApplication;

require 'Library/autoload.php';
require 'Applications/Frontend/FrontendApplication.class.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    protected $appName;
    protected $app;

    public function setUp()
    {
        $app = new FrontendApplication();
        //$app->run();
    }

    public function testx()
    {
        $this->assertEquals(0, 0);
    }
}
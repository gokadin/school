<?php namespace Tests\FrontendTests;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;
//use Applications\School\SchoolApplication;
use Applications\Backend\BackendApplication;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected $appName;
    protected $app;

    public function setUp()
    {
        $this->appName = $this->appName.'Application';
        $app = new $this->appName();
        $app->run();
    }
}
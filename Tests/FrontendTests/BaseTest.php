<?php namespace Tests\FrontendTests;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        @session_start();

        $this->app = new FrontendApplication;
    }
}
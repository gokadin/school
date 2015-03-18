<?php namespace Tests\FrameworkTests;

use PHPUnit_Framework_TestCase;
use Library\Application;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        \Library\Config::temporary('testing', 'true');
        $this->app = new Application('test');
    }
}
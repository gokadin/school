<?php namespace Tests\FrontendTest;

use PHPUnit_Framework_TestCase;
use Applications\Frontend\FrontendApplication;

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        \Library\Config::temporary('testing', 'true');
        $this->app = new FrontendApplication('Frontend');
    }
}
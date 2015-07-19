<?php namespace Tests\ApplicationTest;

use Library\Application;
use Library\Config;
use PHPUnit_Framework_TestCase;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Config::temporary('testing', 'true');
        new Application();
    }
}
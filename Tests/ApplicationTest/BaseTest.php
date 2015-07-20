<?php namespace Tests\ApplicationTest;

use Library\Application;
use PHPUnit_Framework_TestCase;

require __DIR__ . '/../../Bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        new Application();
    }
}
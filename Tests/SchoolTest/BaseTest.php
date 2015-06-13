<?php namespace Tests\SchoolTest;

use PHPUnit_Framework_TestCase;
use Applications\School\SchoolApplication;
use Library\Application;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Library\Config::temporary('testing', 'true');
        new SchoolApplication('School');
    }
}
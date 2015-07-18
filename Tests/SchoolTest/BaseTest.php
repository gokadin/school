<?php namespace Tests\SchoolTest;

use Library\Config;
use PHPUnit_Framework_TestCase;
use Applications\School\SchoolApplication;

require __DIR__.'/../../bootstrap/autoload.php';

abstract class BaseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Config::temporary('testing', 'true');
        $app = new SchoolApplication('School');
    }
}
<?php

namespace Library\Testing;

use PHPUnit_Framework_TestCase;
use Library\Facades\Facade;

require __DIR__.'/../../Bootstrap/autoload.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        putenv('APP_ENV=testing');

        Facade::resetResolvedInstances();
    }
}
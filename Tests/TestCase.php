<?php

namespace Tests;

use Library\Application;

class TestCase extends \Library\Testing\TestCase
{
    public function createApplication()
    {
        require __DIR__.'/../Bootstrap/autoload.php';

        return new Application();
    }
}
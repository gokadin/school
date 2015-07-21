<?php

namespace Tests;

use Library\Application;

class TestCase extends \Library\Testing\TestCase
{
    public function createApplication()
    {
        return new Application();
    }
}
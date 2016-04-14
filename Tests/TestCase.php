<?php

namespace Tests;

use Library\Application;

class TestCase extends \Library\Testing\TestCase
{
    public function setUp()
    {
        parent::setUp();

        require __DIR__.'/../Bootstrap/autoload.php';
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function createApplication()
    {
        return new Application();
    }
}
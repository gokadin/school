<?php namespace Tests\FrontendTests;

use Tests\BaseTest;

class ExampleTests extends BaseTest
{
    public function setUp()
    {
        $this->appName = 'Frontend';
        parent::setUp();
    }

    public function testIsWorking()
    {
        $x = 3;
        $this->assertEquals(3, $x, 'message');
    }
}
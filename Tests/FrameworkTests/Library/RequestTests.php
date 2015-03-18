<?php namespace Tests\FrameworkTests\Library;

use Library\Facades\Request;
use Tests\FrameworkTests\BaseTest;

class RequestTests extends BaseTest
{
    public function testThatGetRequestDataIsSafe()
    {
        $_GET['test'] = 'hello';

        $this->assertEquals('hello', Request::getData('test'));
        $this->assertTrue(false); // to finish
    }
}
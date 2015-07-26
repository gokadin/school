<?php

namespace Tests\FrameworkTest\Routing;

use Library\Facades\Request;
use Tests\FrameworkTest\BaseTest;
use Library\Routing\Route;

class RouteTest extends BaseTest
{
    public function testMatchWithSimpleRequestWhenValid()
    {
        // Arrange
        $_SERVER['REQUEST_URI'] = '/test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $route = new Route(['GET'], '/test', 'controller@action', '', []);

        // Assert
        $this->assertTrue($route->matches(Request::instance()));
    }
}
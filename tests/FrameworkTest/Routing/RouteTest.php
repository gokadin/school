<?php

namespace Tests\FrameworkTest\Routing;

use Library\Http\Request;
use Tests\FrameworkTest\BaseTest;
use Library\Routing\Route;

class RouteTest extends BaseTest
{
    public function testMatchWithSimpleRequestWhenValid()
    {
        // Arrange
        $request = new Request('GET', '/test', []);
        $route = new Route(['GET'], '/test', 'controller@action', '', []);

        // Assert
        $this->assertTrue($route->matches($request));
    }
}
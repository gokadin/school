<?php

namespace Tests\FrameworkTest\Library\Routing;

use Library\Routing\Route;
use Library\Routing\RouteCollection;
use Tests\FrameworkTest\BaseTest;

class RouteCollectionTest extends BaseTest
{
    public function testThatWhenIAddARouteItIsAddedToCollection()
    {
        // Arrange
        $collection = new RouteCollection();

        // Act
        $collection->add(new Route(['GET'], '/test', 'controller@action'));

        // Assert
        $this->assertEquals(1, count($collection));
    }
}
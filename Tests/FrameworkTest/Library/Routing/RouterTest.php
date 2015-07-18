<?php

namespace Tests\FrameworkTest\Library\Routing;

use Library\Routing\Router;
use Tests\FrameworkTest\BaseTest;

class RouterTest extends BaseTest
{
    public function testThatGetRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->get('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPostRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->post('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPutRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->put('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPatchRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->patch('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatDeleteRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->delete('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatManyRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router();

        // Act
        $router->many(['GET', 'POST'], '/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }
}
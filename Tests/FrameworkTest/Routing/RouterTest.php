<?php

namespace Tests\FrameworkTest\Routing;

use Library\Routing\Router;
use Tests\FrameworkTest\BaseTest;

class RouterTest extends BaseTest
{
    public function testThatGetRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->get('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPostRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->post('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPutRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->put('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatPatchRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->patch('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatDeleteRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->delete('/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }

    public function testThatManyRouteIsCorrectlyRegistered()
    {
        // Arrange
        $router = new Router($this->app->container());

        // Act
        $router->many(['GET', 'POST'], '/test', ['as' => 'test', 'uses' => 'controller@index']);

        // Assert
        $this->assertTrue($router->has('test'));
    }
}
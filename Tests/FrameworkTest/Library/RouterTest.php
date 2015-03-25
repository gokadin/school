<?php namespace Tests\FrameworkTest\Library;

use Library\Facades\App;
use Tests\FrameworkTest\BaseTest;
use Library\Facades\Router;
use Library\Route;

class RouterTest extends BaseTest
{
    public function testThatRouteUrlsAreCorrectlyMatched()
    {
        // Arrange
        $route = new Route('test', 'Index', 'index', '/test', 'GET');

        // Act
        $matches = $route->match('test', '/test', 'GET');

        // Assert
        $this->assertTrue($matches);
    }

    public function testThatGetRoutesAreCorrectlyFound()
    {
        // Arrange
        $url1 = '/test';
        $method1 = 'GET';
        $url2 = '/test/{0}/{1}';
        $method2 = 'GET';

        // Act
        $route1 = Router::getRoute(App::name(), $url1, $method1);
        $route2 = Router::getRoute(App::name(), $url2, $method2);

        // Assert
        $this->assertEquals($url1, $route1->url());
        $this->assertEquals($url2, $route2->url());
    }

    public function testThatPostRoutesAreCorrectlyFound()
    {
        // Arrange
        $url = '/test';
        $method = 'POST';

        // Act
        $route = Router::getRoute(App::name(), $url, $method);

        // Assert
        $this->assertEquals($url, $route->url());
    }

    public function testThatPutRoutesAreCorrectlyFound()
    {
        // Arrange
        $url = '/test';
        $method = 'PUT';

        // Act
        $route = Router::getRoute(App::name(), $url, $method);

        // Assert
        $this->assertEquals($url, $route->url());
    }

    public function testThatDeleteRoutesAreCorrectlyFound()
    {
        // Arrange
        $url = '/test';
        $method = 'DELETE';

        // Act
        $route = Router::getRoute(App::name(), $url, $method);

        // Assert
        $this->assertEquals($url, $route->url());
    }

    public function testThatMethodNamesCanBeLowerCase()
    {
        // Arrange
        $url = '/test';
        $method = 'get';

        // Act
        $route = Router::getRoute(App::name(), $url, $method);

        // Assert
        $this->assertEquals($url, $route->url());
    }
}
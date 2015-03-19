<?php namespace Tests\FrameworkTest\Library;

use Tests\FrameworkTest\BaseTest;
use Library\Route;

class RouteTest extends BaseTest
{
    public function testThatRouteUrlsAreCorrectlyMatched()
    {
        // Arrange
        $route1 = new Route('test', 'Index', 'index', '/test', 'GET');
        $route2 = new Route('test', 'Index', 'index', '/test/{0}/{1}/{0}/other', 'DELETE', ['one', 'two']);

        // Act
        $matches1 = $route1->match('test', '/test', 'GET');
        $matches2 = $route2->match('test', '/test/10/string/10/other', 'DELETE');

        // Assert
        $this->assertTrue($matches1);
        $this->assertTrue($matches2);
    }

    public function testThatActionsAreCorrectlyMatched()
    {
        // Arrange
        $route = new Route('test', 'Index', 'index', '/test', 'GET');

        // Act
        $matches = $route->matchAction($this->app->name(), 'Index', 'index');

        // Assert
        $this->assertTrue($matches);
    }

    public function testThatRouteIsAbleToResolveAnUrlWithArgs()
    {
        // Arrange
        $route1 = new Route('test', 'Index', 'index', '/test/{0}', 'GET', ['var1']);
        $route2 = new Route('test', 'Index', 'index', '/test/{0}/other/{1}', 'GET', ['var1', 'var2']);
        $route3 = new Route('test', 'Index', 'index', '/test/{1}/other/{0}/{1}', 'GET', ['var1', 'var2']);

        // Act
        $url1 = $route1->resolveUrl(10);
        $url2 = $route2->resolveUrl([10, 'abc']);
        $url3 = $route3->resolveUrl([10, 'abc']);

        // Assert
        $this->assertEquals('/test/10', $url1);
        $this->assertEquals('/test/10/other/abc', $url2);
        $this->assertEquals('/test/abc/other/10/abc', $url3);
    }
}
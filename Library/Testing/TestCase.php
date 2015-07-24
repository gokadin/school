<?php

namespace Library\Testing;

use Library\Facades\Request;
use Library\Facades\Router;
use Library\Facades\Session;
use PHPUnit_Framework_TestCase;
use Mockery;
use Library\Facades\Facade;

require __DIR__.'/../../Bootstrap/autoload.php';

class TestCase extends PHPUnit_Framework_TestCase
{
    protected $tearDownCallbacks = [];
    protected $includeToken = true;

    public function setUp()
    {
        putenv('APP_ENV=testing');

        Facade::resetResolvedInstances();
    }

    public function skipToken()
    {
        $this->includeToken = false;
    }

    public function get($routeName, $arguments = [])
    {
        if (!Router::has($routeName))
        {
            return '';
        }

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = Router::getUri($routeName, $arguments);

        return Router::dispatch(Request::instance());
    }

    public function post($routeName, $arguments = [])
    {
        $this->createRequest('POST', $routeName, $arguments);
    }

    public function put($routeName, $arguments = [])
    {
        $this->createRequest('PUT', $routeName, $arguments);
    }

    public function patch($routeName, $arguments = [])
    {
        $this->createRequest('PATCH', $routeName, $arguments);
    }

    public function delete($routeName, $arguments = [])
    {
        $this->createRequest('DELETE', $routeName, $arguments);
    }

    private function createRequest($method, $routeName, $arguments)
    {
        if (!Router::has($routeName))
        {
            return '';
        }

        $_SERVER['REQUEST_URI'] = Router::getUri($routeName);
        $_POST = array_merge($_POST, $arguments);
        $method == 'POST'
            ? $_SERVER['REQUEST_METHOD'] = 'POST'
            : $_POST['_method'] = $method;

        if ($this->includeToken)
        {
            $_POST['_token'] = Session::generateToken();
        }

        return Router::dispatch(Request::instance());
    }

    public function action($method, $controllerAndAction, $arguments = [])
    {
        list($controllerName, $methodName) = explode('@', $controllerAndAction);
        $controllerName = '\\App\\Http\\Controllers\\'.$controllerName;
        $controller = new $controllerName();

        $_SERVER['REQUEST_METHOD'] = $method;
        switch ($method)
        {
            case 'GET':
                $_GET = array_merge($_GET, $arguments);
                break;
            default:
                $_POST = array_merge($_POST, $arguments);
                $_POST['_method'] = $method;
                break;
        }

        if ($method == 'GET' && sizeof($arguments) > 0)
        {
            return call_user_func_array([$controller, $methodName], $arguments);
        }

        return $controller->$methodName();
    }

    public function tearDown()
    {
        if (class_exists('Mockery')) {
            Mockery::close();
        }

        foreach ($this->tearDownCallbacks as $callback)
        {
            call_user_func($callback);
        }
    }

    protected function addTearDownCallback(callable $callback)
    {
        $this->tearDownCallbacks[] = $callback;
    }
}
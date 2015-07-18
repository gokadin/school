<?php

namespace Library\Routing;

use Library\Facades\Response;
use Library\Request;

class Router
{
    protected $routes;
    protected $currentRoute;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->currentRoute = null;
    }

    public function get($uri, $action)
    {
        $this->addRoute(['GET'], $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute(['POST'], $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->addRoute(['PUT'], $uri, $action);
    }

    public function patch($uri, $action)
    {
        $this->addRoute(['PATCH'], $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->addRoute(['DELETE'], $uri, $action);
    }

    public function many($methods, $uri, $action)
    {
        $this->addRoute($methods, $uri, $action);
    }

    protected function addRoute($methods, $uri, $action)
    {
        $this->routes->add(new Route($methods, $uri, $action));
    }

    public function has($name)
    {
        return $this->routes->hasNamedRoute($name);
    }

    public function dispatch(Request $request)
    {
        $this->currentRoute = $this->findRoute($request);

        return $this->executeRouteAction();
    }

    protected function findRoute(Request $request)
    {
        try
        {
            return $this->routes->match($request);
        }
        catch (RouteNotFoundException $e)
        {
            Response::redirect404();
        }
    }

    protected function executeRouteAction()
    {
        $action = $this->currentRoute->action();

        if (is_string($action))
        {
            return $this->callControllerFromString($action);
        }

        if (is_array($action))
        {
            return $this->executeArrayAction($action);
        }

        if (is_callable($action))
        {
            return $action();
        }
    }

    protected function executeArrayAction($action)
    {
        if (isset($action['uses']))
        {
            return $this->callControllerFromString($action['uses']);
        }
    }

    protected function callControllerFromString($action)
    {
        list($controllerName, $methodName) = explode('@', $action);
        $controllerName = '\\app\\Http\\Controllers\\'.$controllerName.'.php';

        return $this->executeController($controllerName, $methodName);
    }

    protected function executeController($controllerName, $methodName)
    {
        return call_user_func_array($controllerName, $methodName);
    }
}
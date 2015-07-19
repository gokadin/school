<?php

namespace Library\Routing;

use Library\Facades\Response;
use Library\Request;
use SplStack;

class Router
{
    protected $routes;
    protected $currentRoute;
    protected $namespaces;
    protected $prefixes;
    protected $middlewares;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->currentRoute = null;
        $this->namespaces= array();
        $this->prefixes = array();
        $this->middlewares = array();
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

    public function group($params, $action)
    {
        if (isset($params['namespace']))
        {
            array_push($this->namespaces, $params['namespace']);
        }

        if (isset($params['prefix']))
        {
            array_push($this->prefixes, $params['prefix']);
        }

        if (isset($params['middleware']))
        {
            array_push($this->middlewares, $params['middleware']);
        }

        $action();

        if (isset($params['namespace']))
        {
            array_pop($this->namespaces);
        }

        if (isset($params['prefix']))
        {
            array_pop($this->prefixes);
        }

        if (isset($params['middleware']))
        {
            array_pop($this->middlewares);
        }
    }

    protected function addRoute($methods, $uri, $action)
    {
        if (sizeof($this->namespaces) > 0)
        {
            $namespaceString = '';
            for ($i = 0; $i < sizeof($this->namespaces); $i++)
            {
                $namespaceString .= $this->namespaces[$i].'\\';
            }

            if (is_string($action))
            {
                $action = $namespaceString.$action;
            }
            else if (is_array($action) && isset($action['uses']))
            {
                $action['uses'] = $namespaceString.$action['uses'];
            }
        }

        if (sizeof($this->prefixes) > 0)
        {
            $prefixString = '';
            for ($i = 0; $i < sizeof($this->prefixes); $i++)
            {
                $prefixString .= $this->prefixes[$i];
            }

            $uri = $prefixString.$uri;
        }

        $middlewares = array();
        if (sizeof($this->middlewares > 0))
        {
            foreach ($this->middlewares as $middleware)
            {
                if (!is_array($middleware))
                {
                    $middlewares[] = $middleware;
                    continue;
                }

                foreach ($middleware as $m)
                {
                    $middlewares[] = $m;
                }
            }
        }
        $this->routes->add(new Route($methods, $uri, $action, $middlewares));
    }

    public function has($name)
    {
        return $this->routes->hasNamedRoute($name);
    }

    public function dispatch(Request $request)
    {
        $this->currentRoute = $this->findRoute($request);

        return $this->executeRouteAction($request);
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

    protected function executeRouteAction(Request $request)
    {
        $action = $this->currentRoute->action();

        $actionClosure = function() { return ''; };

        if (is_string($action))
        {
            $actionClosure = $this->getControllerClosure($action);
        }

        if (is_array($action))
        {
            $actionClosure = $this->getArrayClosure($action);
        }

        if (is_callable($action))
        {
            $actionClosure = function() use ($action) {
                return call_user_func_array($action, $this->currentRoute->parameters());
            };
        }

        return $this->executeActionClosure($actionClosure, $request);
    }

    protected function executeActionClosure($closure, Request $request)
    {
        if (sizeof($this->currentRoute->middlewares()) == 0)
        {
            return $closure();
        }

        $closure = $this->getActionClosureWithMiddlewares($closure, $request, sizeof($this->currentRoute->middlewares()) - 1);

        return $closure();
    }

    protected function getActionClosureWithMiddlewares($closure, Request $request, $index)
    {
        $middlewareName = '\\App\\Http\\Middleware\\'.$this->currentRoute->middlewares()[$index];
        $middleware = new $middlewareName();

        if ($index == 0)
        {
            return function() use ($middleware, $closure, $request) {
                return $middleware->handle($request, $closure);
            };
        }

        return $this->getActionClosureWithMiddlewares(function() use ($middleware, $closure, $request) {
            return $middleware->handle($request, $closure);
        }, $request, $index - 1);
    }

    protected function getArrayClosure($action)
    {
        if (isset($action['uses']))
        {
            return $this->getControllerClosure($action['uses']);
        }
    }

    protected function getControllerClosure($action)
    {
        list($controllerName, $methodName) = explode('@', $action);

        return function() use ($controllerName, $methodName) {
            return call_user_func_array([$controllerName, $methodName], $this->currentRoute->parameters());
        };
    }
}
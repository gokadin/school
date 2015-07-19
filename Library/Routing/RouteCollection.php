<?php

namespace Library\Routing;

use Library\Request;

class RouteCollection implements \Countable
{
    protected $allRoutes;
    protected $routesByMethod;
    protected $nameList;

    public function __construct()
    {
        $this->allRoutes = array();
        $this->routesByMethod = array();
        $this->nameList = array();
    }

    public function add(Route $route)
    {
        $this->allRoutes[] = $route;

        foreach ($route->methods() as $method)
        {
            $this->routesByMethod[$method][] = $route;
        }

        $action = $route->action();
        if (is_array($action) && isset($action['as']))
        {
            $this->nameList[$action['as']] = $route;
        }
    }

    public function match(Request $request)
    {
        $method = $request->method();

        foreach ($this->routesByMethod[$method] as $route)
        {
            if ($route->matches($request))
            {
                return $route;
            }
        }

        throw new RouteNotFoundException('Route for uri '.$request->uri().' and method '.$method.' not found.');
    }

    public function hasNamedRoute($name)
    {
        return isset($this->nameList[$name]);
    }

    public function count()
    {
        return count($this->allRoutes);
    }
}
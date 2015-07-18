<?php

namespace Library\Routing;

use Library\Request;

class Route
{
    protected $methods;
    protected $uri;
    protected $action;
    protected $middlewares;

    public function __construct($methods, $uri, $action, $middlewares)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
        $this->middlewares = $middlewares;
    }

    public function methods()
    {
        return $this->methods;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function action()
    {
        return $this->action;
    }

    public function middlewares()
    {
        return $this->middlewares;
    }

    public function matches(Request $request)
    {
        return $request->requestURI() == $this->uri && in_array($request->method(), $this->methods);
    }
}
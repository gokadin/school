<?php

namespace Library\Routing;

use Library\Http\Request;

class Route
{
    protected $methods;
    protected $uri;
    protected $action;
    protected $middlewares;
    protected $parameters;

    public function __construct($methods, $uri, $action, $middlewares)
    {
        $this->methods = $methods;
        $this->uri = $uri;
        $this->action = $action;
        $this->middlewares = $middlewares;
        $this->parameters = array();
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

    public function parameters()
    {
        return $this->parameters;
    }

    public function matches(Request $request)
    {
        if (!in_array($request->method(), $this->methods))
        {
            return false;
        }

        $pattern = '({[a-zA-Z0-9]+})';
        $substituteUrl = preg_replace($pattern, '(.+)', $this->uri);

        if (preg_match('`^'.strtolower($substituteUrl).'$`', strtolower($request->uri()), $valueMatches) != 1)
        {
            return false;
        }

        $pattern = '/{([a-zA-Z0-9]+)}/';
        preg_match_all($pattern, $this->uri, $varMatches);

        for ($i = 0; $i < sizeof($valueMatches) - 1; $i++)
        {
            if (isset($varMatches[$i]))
            {
                $this->parameters[$varMatches[1][$i]] = $valueMatches[$i + 1];
            }
        }

        $this->populateGetArray();

        return true;
    }

    private function populateGetArray()
    {
        $_GET = array_merge($_GET, $this->parameters);
    }
}
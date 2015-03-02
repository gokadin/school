<?php
namespace Library;

class Router
{
    protected $routes = array();

    const NO_ROUTE = 1;

    public function addRoute(Route $route) {
        if (!in_array($route, $this->routes)) {
            $this->routes[] = $route;
        }
    }

    public function getRoute($url, $method) {
        foreach ($this->routes as $route) {
            if (($varsValues = $route->match($url, $method)) !== false) {
                if ($route->hasVars()) {
                    $varsNames = $route->varsNames();
                    $listVars = array();

                    foreach ($varsValues as $key => $match) {
                        if ($key !== 0) {
                            $listVars[$varsNames[$key - 1]] = $match;
                        }
                    }
                    $route->setVars($listVars);
                }
                return $route;
            }
        }
        throw new \RuntimeException('No routes found corresponding to the URL', self::NO_ROUTE);
    }
}
?>
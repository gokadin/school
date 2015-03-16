<?php namespace Library;

use Symfony\Component\Yaml\Exception\RuntimeException;

class Router
{
    protected $routes = array();

    const NO_ROUTE = 1;

    public function addRoute(Route $route)
    {
        if (!in_array($route, $this->routes))
            $this->routes[] = $route;
    }

    public function getRoute($url, $method)
    {
        foreach ($this->routes as $route)
        {
            if (($varsValues = $route->match($url, $method)) !== false)
            {
                if ($route->hasVars())
                {
                    $varsNames = $route->varsNames();
                    $listVars = array();

                    foreach ($varsValues as $key => $match)
                    {
                        if ($key !== 0)
                            $listVars[$varsNames[$key - 1]] = $match;
                    }
                    $route->setVars($listVars);
                }
                return $route;
            }
        }

        throw new RuntimeException('No routes found corresponding to the URL', self::NO_ROUTE);
    }

    public function actionToPath($string, $args = null)
    {
        $arr = explode('#', $string);
        $app = $arr[0];
        $module = $arr[1];
        $action = $arr[2];

        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../Config/routes.xml');

        $applications = $xml->getElementsByTagName('application');
        $routes = array();
        foreach ($applications as $application)
        {
            if ($application->getAttribute('name') == $app)
            {
                $routes = $application->getElementsByTagName('route');
                break;
            }
        }

        if ($routes == null)
            throw new RuntimeException('Route not found: '.$string.'.');

        foreach ($routes as $route)
        {
            if ($route->getAttribute('module') == $module &&
                $route->getAttribute('action') == $action)
            {
                if (!$route->hasAttribute('vars'))
                    return $route->getAttribute('url');
                else
                {
                    return preg_replace('/\/\(.+\)\/*/', $args, $route->getAttribute('url'));
                }
            }
        }

        throw new RuntimeException('Router.actionToPath : route '.$string.' not found.');
    }
}

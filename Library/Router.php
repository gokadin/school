<?php namespace Library;

use Symfony\Component\Yaml\Exception\RuntimeException;

class Router
{
    const NO_ROUTE = 1;

    protected $routes = array();

    protected function populateRoutes()
    {
        if ($this->routes != null)
            return;

        $xml = new \DOMDocument;
        if (Config::get('frameworkTesting') == 'true')
            $xml->load(__DIR__.'/../Tests/'.Config::get('testApplication').'Test/Config/routes.xml');
        else
            $xml->load(__DIR__.'/../Config/routes.xml');

        $applications = $xml->getElementsByTagName('application');
        foreach ($applications as $application)
        {
            $appName = $application->getAttribute('name');
            $routes = $application->getElementsByTagName('route');
            foreach ($routes as $route)
            {
                $vars = array();

                if ($route->hasAttribute('vars'))
                    $vars = explode(',', $route->getAttribute('vars'));
                
                $vars = array_map('trim', $vars);
                $this->routes[] = (new Route(
                    $appName,
                    $route->getAttribute('module'),
                    $route->getAttribute('action'),
                    $route->getAttribute('url'),
                    $route->getAttribute('method'),
                    $vars));
            }
        }
    }

    public function getRoute($appName, $url, $method)
    {
        $this->populateRoutes();

        foreach ($this->routes as $route)
        {
            if ($route->match($appName, $url, $method))
            {
                $route->resolveUrl();
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

        $this->populateRoutes();

        foreach ($this->routes as $route)
        {
            if ($route->matchAction($app, $module, $action))
            {
                if (!$route->hasVars())
                    return $route->url();
                else
                {
                    if ($args == null)
                        throw new RuntimeException('Router.actionToPath : route '.$route->url().' requires arguments.');

                    return $route->resolveUrl($args);
                }
            }
        }

        throw new RuntimeException('Router.actionToPath : route '.$string.' not found.');
    }
}

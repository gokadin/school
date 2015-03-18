<?php namespace Library;

use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Router;
use Library\Facades\Facade;
use Library\Session;
use Library\Database\Database;

class Application
{
    private $container;
    protected $name;

    public function __construct($name)
    {
        Facade::setFacadeApplication($this);

        $this->name = $name;

        $this->container = new Container();
        $this->ConfigureContainer();

        $this->container()->instance('session', new Session());
    }

    protected function ConfigureContainer()
    {
        $this->container->instance('app', $this);
        $containerConfiguration = new ContainerConfiguration($this->container);
        $containerConfiguration->configureContainer();
    }

    public function container()
    {
        if ($this->container == null)
        {
            $this->container = new Container();
            $this->ConfigureContainer();
        }

        return $this->container;
    }

    public function name()
    {
        return $this->name;
    }

    public function getController()
    {
        $xml = new \DOMDocument;
        $xml->load(__DIR__.'/../Config/routes.xml');

        $applications = $xml->getElementsByTagName('application');
        $routes = array();
        foreach ($applications as $application)
        {
            if ($application->getAttribute('name') == $this->name)
            {
                $routes = $application->getElementsByTagName('route');
                break;
            }
        }

        if ($routes == null)
            throw new \Exception("Application.getController : could not find routes.");

        foreach ($routes as $route)
        {
            $vars = array();

            if ($route->hasAttribute('vars'))
                $vars = explode(',', $route->getAttribute('vars'));

            Router::addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('method'), $route->getAttribute('action'), $vars));
        }

        try
        {
            $matchedRoute = Router::getRoute(Request::requestURI(), Request::method());
        }
        catch (\RuntimeException $e)
        {
            if ($e->getCode() == Router::NO_ROUTE) {
                Request::redirect404();
            }
        }

        $_GET = array_merge($_GET, $matchedRoute->vars());

        $controllerPrefix = $matchedRoute->module();
        if (strpos($controllerPrefix, '\\'))
            $controllerPrefix = strstr($controllerPrefix, '\\');
        if (substr($controllerPrefix, 0, 1) == '\\')
            $controllerPrefix = substr($controllerPrefix, 1);

        if (strpos($controllerPrefix, '/'))
            $controllerPrefix = strstr($controllerPrefix, '/');
        if (substr($controllerPrefix, 0, 1) == '/')
            $controllerPrefix = substr($controllerPrefix, 1);

        $controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.str_replace('/', '\\', $matchedRoute->module()).'\\'.$controllerPrefix.'Controller';
        return new $controllerClass($this, str_replace('\\', '/', $matchedRoute->module()), $matchedRoute->method(), $matchedRoute->action());
    }	
    
    public function run()
    {
        $controller = $this->getController();

        $controller->execute();
        Response::send();
    }

    public function router()
    {
        return $this->router;
    }
}	

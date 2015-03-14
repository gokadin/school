<?php namespace Library;

use Library\Container\Container;
use Library\Router;
use Library\Facades\Facade;
use Library\Session;
use Library\Database\Database;

abstract class Application extends Container {
    protected $httpRequest;
    protected $httpResponse;
    protected $router;
    protected $name;
    protected $session;
    protected $config;

    public function __construct()
    {
        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);
        $this->router = new Router();
        $this->name = '';
        $this->session = new Session();
        $this->config = new Config($this);

        Facade::setFacadeApplication($this);
        $this->instance('app', $this);
        $this->instance('response', $this->httpResponse);
        $this->instance('request', $this->httpRequest);
        $this->instance('config', $this->config);
        $this->instance('session', $this->session);
        $this->instance('database', new Database(PDOFactory::conn()));
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

            $this->router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('method'), $route->getAttribute('action'), $vars));
        }

        try
        {
            $matchedRoute = $this->router->getRoute($this->httpRequest->requestURI(), $this->httpRequest->method());
        }
        catch (\RuntimeException $e)
        {
            if ($e->getCode() == Router::NO_ROUTE) {
                $this->httpResponse->redirect404();
            }
        }

        $_GET = array_merge($_GET, $matchedRoute->vars());

        $controllerPrefix = $matchedRoute->module();
        if (strpos($controllerPrefix, '\\'))
            $controllerPrefix = strstr($matchedRoute->module(), '\\');
        if (substr($controllerPrefix, 0, 1) == '\\')
            $controllerPrefix = substr($controllerPrefix, 1);
        $controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.$matchedRoute->module().'\\'.$controllerPrefix.'Controller';
        return new $controllerClass($this, str_replace('\\', '/', $matchedRoute->module()), $matchedRoute->method(), $matchedRoute->action());
    }	
    
    abstract public function run();

    public function name()
    {
        return $this->name;
    }

    public function router()
    {
        return $this->router;
    }

    public function make($abstract, $parameters = [])
    {
        return parent::make($abstract, $parameters);
    }
}	

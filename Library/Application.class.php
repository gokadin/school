<?php
namespace Library;

use Library\Container\Container;
use Library\Facades\Facade;

abstract class Application extends Container {
    protected $httpRequest;
    protected $httpResponse;
    protected $name;
    protected $user;
    protected $config;

    public function __construct() {
        Facade::setFacadeApplication($this);
        $this->instance('app', $this);


        $this->httpRequest = new HTTPRequest($this);
        $this->httpResponse = new HTTPResponse($this);$this->instance('response', $this->httpResponse);
        $this->name = '';
        $this->user = new \Library\User($this);
        $this->config = new Config($this);
    }

    public function getController() {
        $router = new \Library\Router;

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

        foreach ($routes as $route) {
            $vars = array();

            if ($route->hasAttribute('vars')) {
                $vars = explode(',', $route->getAttribute('vars'));
            }

            $router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('method'), $route->getAttribute('action'), $vars));
        }

        try
        {
            $matchedRoute = $router->getRoute($this->httpRequest->requestURI(), $this->httpRequest->method());
        }
        catch (\RuntimeException $e)
        {
            if ($e->getCode() == \Library\Router::NO_ROUTE) {
                $this->httpResponse->redirect404();
            }
        }

        $_GET = array_merge($_GET, $matchedRoute->vars());

        $controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.$matchedRoute->module().'\\'.$matchedRoute->module().'Controller';
        return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->method(), $matchedRoute->action());
    }	
    
    abstract public function run();

    public function request() {
        return $this->httpRequest;
    }

    public function response() {
        return $this->httpResponse;
    }

    public function name() {
        return $this->name;
    }
    
    public function user() {
        return $this->user;
    }
    
    public function config() {
        return $this->config;
    }

    public function make($abstract, $parameters = [])
    {
        return parent::make($abstract, $parameters);
    }
}	
?>
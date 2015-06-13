<?php namespace Library;

use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Router;
use Library\Facades\Facade;

class Application
{
    private $container;
    protected $name;
    protected $module;
    protected $method;
    protected $action;

    public function __construct($name)
    {
        Facade::setFacadeApplication($this);

        $this->name = $name;
        $this->module = null;
        $this->method = null;
        $this->action = null;

        $this->container = new Container();
        $this->ConfigureContainer();
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
    
    public function module()
    {
        return $this->module;
    }
    
    public function method()
    {
        return $this->method;
    }
    
    public function action()
    {
        return $this->action;
    }
    
    public function processRoute()
    {
        $matchedRoute = null;
        try
        {
            $matchedRoute = Router::getRoute($this->name, Request::requestURI(), Request::method());
        }
        catch (\RuntimeException $e)
        {
            if ($e->getCode() == \Library\Router::NO_ROUTE)
                Response::redirect404();
        }
        
        $_GET = array_merge($_GET, $matchedRoute->vars());
        
        $this->module = str_replace('\\', '/', $matchedRoute->module());
        $this->method = $matchedRoute->method();
        $this->action = $matchedRoute->action();
    }

    public function getController()
    {
        $controllerPrefix = $this->module();
        if (strpos($controllerPrefix, '\\'))
            $controllerPrefix = strstr($controllerPrefix, '\\');
        if (substr($controllerPrefix, 0, 1) == '\\')
            $controllerPrefix = substr($controllerPrefix, 1);

        if (strpos($controllerPrefix, '/'))
            $controllerPrefix = strstr($controllerPrefix, '/');
        if (substr($controllerPrefix, 0, 1) == '/')
            $controllerPrefix = substr($controllerPrefix, 1);

        $controllerClass = 'Applications\\'.$this->name.'\\Modules\\'.str_replace('/', '\\', $this->module()).'\\'.$controllerPrefix.'Controller';
        return new $controllerClass();
    }	
    
    public function run()
    {
        $this->processRoute();
        $controller = $this->getController();
        
        require 'Web/lang/common.php';
        $this->setLang($lang);
        \Library\Facades\Page::add('lang', $lang);
        
        $controller->execute();
        Response::send();
    }
}	

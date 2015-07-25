<?php

namespace Library;

use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Request;
use Library\Facades\Router;
use Library\Facades\Facade;
use Library\Http\View;

class Application
{
    protected $basePath;
    protected $container;
    protected $viewToSend;

    public function __construct()
    {
        Facade::setFacadeApplication($this);

        $this->container = new Container();
        $this->viewToSend = null;
        $this->basePath = __DIR__.'/../';

        $this->ConfigureContainer();

        $this->loadRoutes();
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

    private function loadRoutes()
    {
        Router::group(['namespace' => 'App\\Http\\Controllers'], function() {
            require __DIR__ . '/../App/Http/routes.php';
        });
    }

    public function processRoute()
    {
        $result = Router::dispatch(Request::instance());

        $this->viewToSend = $result;
    }

    public function sendView()
    {
        if (is_string($this->viewToSend))
        {
            echo $this->viewToSend;
            return;
        }

        if (!($this->viewToSend instanceof View))
        {
            return;
        }

        $this->viewToSend->send();
    }

    public function basePath()
    {
        return $this->basePath;
    }
}

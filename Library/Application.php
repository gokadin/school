<?php

namespace Library;

use Config\EventRegistration;
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

        $this->configureContainer();
        $this->configureEvents();

        $this->loadRoutes();
    }

    protected function configureContainer()
    {
        $this->container->registerInstance('app', $this);
        $containerConfiguration = new ContainerConfiguration($this->container);
        $containerConfiguration->configureContainer();

        $appContainerConfiguration = new \Config\ContainerConfiguration($this->container);
        $appContainerConfiguration->configureContainer();
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

    protected function configureEvents()
    {
        $eventRegistration = new EventRegistration($this->container()->resolveInstance('eventManager'));

        $eventRegistration->registerEvents();
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
        if (is_string($this->viewToSend) || is_bool($this->viewToSend))
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

<?php

namespace Library;

use Config\EventRegistration;
use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Facade;
use Library\Http\Response;
use Library\Http\View;

class Application
{
    protected $basePath;
    protected $container;
    protected $controllerResponse;

    public function __construct()
    {
        Facade::setFacadeApplication($this);

        $this->container = new Container();
        $this->controllerResponse = null;
        $this->basePath = __DIR__.'/../';

        $this->configureContainer();

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

    private function loadRoutes()
    {
        $this->container()->resolveInstance('router')->group(['namespace' => 'App\\Http\\Controllers'], function() {
            require __DIR__ . '/../App/Http/routes.php';
        });
    }

    public function processRoute()
    {
        $result = $this->container()->resolveInstance('router')->dispatch(
            $this->container()->resolveInstance('request'));

        $this->controllerResponse = $result;
    }

    public function sendView()
    {
        if (!is_object($this->controllerResponse) && !is_array($this->controllerResponse))
        {
            echo $this->controllerResponse;
            exit();
            return;
        }

        if (is_array($this->controllerResponse))
        {
            echo json_encode($this->controllerResponse);
            exit();
            return;
        }

        if ($this->controllerResponse instanceof View)
        {
            $this->controllerResponse->send();
        }

        if ($this->controllerResponse instanceof Response)
        {
            $this->controllerResponse->executeResponse();
        }
    }

    public function basePath()
    {
        return $this->basePath;
    }
}

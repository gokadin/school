<?php

namespace Library;

use Config\EventRegistration;
use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Facade;
use Library\Http\Response;
use Library\Http\View;
use Library\Http\ViewFactory;

class Application
{
    protected $basePath;
    protected $container;
    protected $controllerResponse;

    public function __construct()
    {
        $this->configureErrorHandling();

        Facade::setFacadeApplication($this);

        $this->container = new Container();
        $this->controllerResponse = null;
        $this->basePath = __DIR__.'/../';

        $this->configureContainer();

        $this->loadRoutes();
    }

    private function configureErrorHandling()
    {
        switch (env('APP_DEBUG'))
        {
            case 'true':
                error_reporting(E_ALL);
                break;
            default:
                error_reporting(0);
                break;
        }
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
        $router = $this->container()->resolveInstance('router');

        $router->group(['namespace' => 'App\\Http\\Controllers'], function($router) {
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
            $content = $this->controllerResponse->processView(new ViewFactory($this->container),
                $this->container->resolveInstance('shao'));

            echo $content;
            exit();
            return;
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

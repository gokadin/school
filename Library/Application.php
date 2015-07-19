<?php namespace Library;

use Library\Container\Container;
use Library\Container\ContainerConfiguration;
use Library\Facades\Request;
use Library\Facades\Router;
use Library\Facades\Facade;

class Application
{
    protected $container;
    protected $viewToSend;

    public function __construct()
    {
        Facade::setFacadeApplication($this);

        $this->container = new Container();
        $this->viewToSend = null;

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

    public function processRoute()
    {
        Router::group(['namespace' => 'App\\Http\\Controllers'], function() {
            require __DIR__ . '/../App/Http/routes.php';
        });

        $response = Router::dispatch(Request::instance());

        echo $response;
        //$this->viewToSend =
    }

    public function sendView()
    {

    }
}

<?php namespace Library\Container;

use Library\Config;
use Library\Database\Factory;
use Library\Http\Redirect;
use Library\Http\Request;
use Library\Http\Response;
use Library\Http\View;
use Library\Http\ViewFactory;
use Library\Routing\Router;
use Library\Database\Database;
use Library\Html;
use Library\Form;
use Library\Page;
use Library\Session;
use Library\Shao\Shao;
use Library\Validation\Validator;

class ContainerConfiguration
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function configureContainer()
    {
        $this->container->instance('request', new Request());
        $this->container->instance('response', new Response());
        $this->container->instance('router', new Router());
        $this->container->instance('database', new Database());
        $this->container->instance('html', new HTML());
        $this->container->instance('form', new Form());
        $this->container->instance('session', new Session());
        $this->container->instance('validator', new Validator());
        $this->container->instance('redirect', new Redirect());
        $this->container->instance('shao', new Shao());
        $this->container->instance('viewFactory', new ViewFactory());

        if (Config::get('env') == 'debug')
        {
            $this->configureDebugContainer();
        }
    }

    protected function configureDebugContainer()
    {
        $this->container->instance('modelFactory', new Factory());
    }
}
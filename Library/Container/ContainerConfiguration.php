<?php

namespace Library\Container;

use Library\Configuration\Config;
use Library\Database\Factory;
use Library\Http\Redirect;
use Library\Http\Request;
use Library\Http\Response;
use Library\Http\ViewFactory;
use Library\Log\Log;
use Library\Queue\Queue;
use Library\Routing\Router;
use Library\Database\Database;
use Library\Html;
use Library\Form;
use Library\Page;
use Library\Sentry\Sentry;
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
        $this->container->instance('config', new Config());
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
        $this->container->instance('sentry', new Sentry());
        $this->container->instance('log', new Log());
        $this->container->instance('queue', new Queue());

        if (env('APP_DEBUG'))
        {
            $this->configureDebugContainer();
        }
    }

    protected function configureDebugContainer()
    {
        $this->container->instance('modelFactory', new Factory());
    }
}
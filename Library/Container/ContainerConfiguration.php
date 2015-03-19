<?php namespace Library\Container;

use Library\Request;
use Library\Response;
use Library\Router;
use Library\Database\Database;
use Library\PDOFactory;
use Library\HTML;
use Library\Page;
use Library\Session;

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
        $this->container->instance('database', new Database(PDOFactory::conn()));
        $this->container->instance('html', new HTML());
        $this->container->instance('page', new Page());
        $this->container->instance('session', new Session());
    }
}
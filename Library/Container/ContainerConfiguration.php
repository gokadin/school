<?php namespace Library\Container;

use Library\Request;
use Library\Response;
use Library\Router;
use Library\Database\Database;
use Library\Html;
use Library\Form;
use Library\Page;
use Library\Session;
use Library\Validator;

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
        $this->container->instance('page', new Page());
        $this->container->instance('session', new Session());
        $this->container->instance('validator', new Validator());
    }
}
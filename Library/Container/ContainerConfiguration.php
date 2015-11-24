<?php

namespace Library\Container;

use Library\Configuration\Config;
use Library\Database\ModelFactory;
use Library\Events\EventManager;
use Library\Http\Redirect;
use Library\Http\Request;
use Library\Http\Response;
use Library\Http\View;
use Library\Http\ViewFactory;
use Library\Log\Log;
use Library\Queue\Queue;
use Library\Routing\Router;
use Library\Database\Database;
use Library\DataMapper\DataMapper;
use Library\Http\Form;
use Library\Session\Session;
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
        $app = $this->container->resolveInstance('app');

        $this->container->registerInstance('config', new Config());
        $this->container->registerInstance('request', new Request());
        $this->container->registerInstance('response', new Response());
        $this->container->registerInstance('router', new Router());
        $this->container->registerInstance('form', new Form());
        $this->container->registerInstance('session', new Session());
        $this->container->registerInstance('redirect', new Redirect());
        $this->container->registerInstance('shao', new Shao());
        $this->container->registerInstance('view', new View());
        $this->container->registerInstance('viewFactory', new ViewFactory());
        $this->container->registerInstance('log', new Log());
        $this->container->registerInstance('queue', new Queue());
        $this->container->registerInstance('eventManager', new EventManager());

        // ORM
        $datamapperConfig = require $app->basePath().'Config/datamapper.php';
        $dm = new DataMapper($datamapperConfig);
        $this->container->registerInstance('datamapper', $dm);

        // database
        $databaseConfig = require $app->basePath().'Config/database.php';
        $database = new Database($databaseConfig);
        $this->container->registerInstance('database', $database);

        // Services requiring a database
        $this->container->registerInstance('validator', new Validator($database));
        //$this->container->registerInstance('sentry', new Sentry($dm));

        if (env('APP_DEBUG'))
        {
            $this->configureDebugContainer();
        }
    }

    protected function configureDebugContainer()
    {
        $this->container->registerInstance('modelFactory', new ModelFactory());
    }
}
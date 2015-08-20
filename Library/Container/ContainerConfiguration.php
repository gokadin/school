<?php

namespace Library\Container;

use Library\Configuration\Config;
use Library\Database\DataMapper\DataMapper;
use Library\Database\ModelFactory;
use Library\Events\EventManager;
use Library\Http\Redirect;
use Library\Http\Request;
use Library\Http\Response;
use Library\Http\ViewFactory;
use Library\Log\Log;
use Library\Queue\Queue;
use Library\Redis\Redis;
use Library\Routing\Router;
use Library\Database\Database;
use Library\Http\Form;
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
        $app = $this->container->resolveInstance('app');

        $databaseSettings = require $app->basePath().'Config/database.php';
        $database = new Database($databaseSettings);
        $this->container->registerInstance('database', $database);

        $this->container->registerInstance('config', new Config());
        $this->container->registerInstance('request', new Request());
        $this->container->registerInstance('response', new Response());
        $this->container->registerInstance('router', new Router());
        $this->container->registerInstance('redis', new Redis($databaseSettings));
        $this->container->registerInstance('form', new Form());
        $this->container->registerInstance('session', new Session());
        $this->container->registerInstance('validator', new Validator());
        $this->container->registerInstance('redirect', new Redirect());
        $this->container->registerInstance('shao', new Shao());
        $this->container->registerInstance('viewFactory', new ViewFactory());
        $this->container->registerInstance('sentry', new Sentry());
        $this->container->registerInstance('log', new Log());
        $this->container->registerInstance('queue', new Queue());
        $this->container->registerInstance('eventManager', new EventManager());
        $datamapperSettings = require $app->basePath().'Config/datamapper.php';
        $this->container->registerInstance('dataMapper', new DataMapper($database, $datamapperSettings));

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
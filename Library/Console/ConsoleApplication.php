<?php

namespace Library\Console;

use Library\Console\Modules\DataMapper\DataMapper;
use Library\Console\Modules\Queue\QueueListener;
use Symfony\Component\Console\Application;

class ConsoleApplication
{
    protected $app;
    protected $framework;

    public function __construct()
    {
        $this->app = new Application();
        $this->framework = new \Library\Application();

        putenv('CONSOLE=true');

        $this->addRequiredModules();
    }

    public function run()
    {
        $this->app->run();
    }

    public function addModule($module)
    {
        $this->app->add($module);
    }

    protected function addRequiredModules()
    {
        $database = $this->framework->container()->resolveInstance('database');
        $this->app->add(new QueueListener($database));
    }

    protected function framework()
    {
        return $this->framework;
    }
}
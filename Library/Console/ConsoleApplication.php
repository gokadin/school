<?php

namespace Library\Console;

use Library\Console\Modules\Queue\QueueListener;
use Library\Console\Modules\Routing;
use Symfony\Component\Console\Application;

class ConsoleApplication
{
    protected $app;

    public function __construct()
    {
        $this->app = new Application();

        require __DIR__.'/../../Bootstrap/env.php';

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
        $this->addModule(new QueueListener());
    }
}
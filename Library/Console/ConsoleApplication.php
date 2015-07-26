<?php

namespace Library\Console;

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
        $this->app->add(new QueueListener());
    }

    protected function framework()
    {
        return $this->framework;
    }
}
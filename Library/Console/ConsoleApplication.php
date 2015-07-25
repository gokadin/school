<?php

namespace Library\Console;

use Library\Console\Modules\Routing;
use Symfony\Component\Console\Application;

class ConsoleApplication
{
    protected $app;

    public function __construct()
    {
        $this->app = new Application();

        $this->bootstrapModules();
    }

    public function run()
    {
        $this->app->run();
    }

    protected function bootstrapModules()
    {
        $this->app->add(new Routing());
    }
}
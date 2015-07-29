<?php

namespace Library\Queue;

use Library\Queue\Drivers\SyncQueueDriver;
use Library\Queue\Drivers\DatabaseQueueDriver;

class Queue
{
    protected $driver;
    protected $syncDriver;

    public function __construct()
    {
        $settings = require __DIR__.'/../../Config/queue.php';

        $this->setDrivers($settings);
    }

    protected function setDrivers($settings)
    {
        $this->syncDriver = new SyncQueueDriver();

        switch ($settings['use'])
        {
            case 'database':
                $this->driver = new DatabaseQueueDriver($settings['connections']['database']);
                break;
            default:
                $this->driver = null;
                break;
        }
    }

    public function push($job, $handler = null)
    {
        if (env('CONSOLE'))
        {
            $this->pushSync($job, $handler);
            return;
        }

        $this->driver->push($job, $handler);
    }

    public function pushSync($job, $handler = null)
    {
        $this->syncDriver->push($job, $handler);
    }
}
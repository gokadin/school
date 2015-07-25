<?php

namespace Library\Queue;

use Library\Queue\Drivers\SyncQueueDriver;
use Library\Queue\Drivers\DatabaseQueueDriver;

class Queue
{
    protected $driver;

    public function __construct()
    {
        $settings = require __DIR__.'/../../Config/queue.php';

        $this->setDriver($settings);
    }

    protected function setDriver($settings)
    {
        switch ($settings['use'])
        {
            case 'sync':
                $this->driver = new SyncQueueDriver();
                break;
            case 'database':
                $this->driver = new DatabaseQueueDriver($settings['connections']['database']);
                break;
            default:
                $this->driver = null;
                break;
        }
    }

    public function push(Job $job)
    {
        $this->driver->push($job);
    }
}
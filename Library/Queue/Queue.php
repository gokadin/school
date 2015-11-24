<?php

namespace Library\Queue;

use Library\Queue\Drivers\SyncQueueDriver;
use Library\Queue\Drivers\DatabaseQueueDriver;

class Queue
{
    protected $asyncDriver;
    protected $syncDriver;
    protected $syncOnly;

    public function __construct()
    {
        $settings = require __DIR__.'/../../Config/queue.php';

        $this->syncOnly = false;

        $this->setDrivers($settings);
    }

    protected function setDrivers($settings)
    {
        $this->syncDriver = new SyncQueueDriver();

        switch ($settings['use'])
        {
            case 'database':
                $this->asyncDriver = new DatabaseQueueDriver($settings['connections']['database']);
                break;
            default:
                $this->syncOnly = true;
                $this->asyncDriver = null;
                break;
        }
    }

    public function push($job, $handler = null)
    {
        if ($this->syncOnly || env('CONSOLE') || !($job instanceof ShouldQueue))
        {
            $this->syncDriver->push($job, $handler);
            return;
        }

        $this->asyncDriver->push($job, $handler);
    }
}
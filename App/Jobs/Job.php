<?php

namespace App\Jobs;

use Library\Events\EventManager;
use Library\Queue\Queueable;

abstract class Job
{
    use Queueable;

    protected $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }
}
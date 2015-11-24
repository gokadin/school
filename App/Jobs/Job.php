<?php

namespace App\Jobs;

use App\Events\Event;
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

    protected function fireEvent(Event $event)
    {
        $this->eventManager->fire($event);
    }
}
<?php

namespace App\Domain\Services;

use App\Events\Event;
use App\Jobs\Job;
use Library\Events\EventManager;
use Library\Queue\Queue;

abstract class Service
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(Queue $queue, EventManager $eventManager)
    {
        $this->queue = $queue;
        $this->eventManager = $eventManager;
    }

    protected function dispatchJob(Job $job)
    {
        $this->queue->push($job);
    }

    protected function fireEvent(Event $event)
    {
        $this->eventManager->fire($event);
    }
}
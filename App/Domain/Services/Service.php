<?php

namespace App\Domain\Services;

use App\Events\Event;
use App\Jobs\Job;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

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

    /**
     * @var Transformer
     */
    protected $transformer;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer)
    {
        $this->queue = $queue;
        $this->eventManager = $eventManager;
        $this->transformer = $transformer;
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
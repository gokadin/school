<?php

namespace App\Domain\Services;

use App\Events\Event;
use App\Jobs\Job;
use App\Repositories\Repository;
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

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                Repository $repository)
    {
        $this->queue = $queue;
        $this->eventManager = $eventManager;
        $this->transformer = $transformer;
        $this->repository = $repository;
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
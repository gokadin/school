<?php

namespace App\Domain\Services;

use App\Jobs\Job;
use Library\Queue\Queue;

abstract class Service
{
    /**
     * @var Queue
     */
    protected $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    protected function dispatchJob(Job $job)
    {
        $this->queue->push($job);
    }
}
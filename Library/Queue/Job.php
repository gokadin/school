<?php

namespace Library\Queue;

use Carbon\Carbon;

class Job
{
    protected $executionDate;
    protected $queueName;
    protected $maxAttempts;

    public function __construct()
    {
        $this->executionDate = Carbon::now();
        $this->queueName = null;
        $this->maxAttempts = 3;
    }

    public function after($seconds)
    {
        $this->executionDate->addSeconds($seconds);
        return $this;
    }

    public function onQueue($name)
    {
        $this->queueName = $name;
        return $this;
    }

    public function maxAttempts($count)
    {
        $this->maxAttempts = $count;
        return $this;
    }

    public function getExecutionDate()
    {
        return $this->executionDate;
    }

    public function getQueueName()
    {
        return $this->queueName;
    }

    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }
}
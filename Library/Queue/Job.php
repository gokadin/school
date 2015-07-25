<?php

namespace Library\Queue;

use Carbon\Carbon;

class Job
{
    protected $executionDate;
    protected $queueName;

    public function __construct()
    {
        $this->executionDate = Carbon::now();
        $this->queueName = null;
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

    public function executionDate()
    {
        return $this->executionDate();
    }

    public function queueName()
    {
        return $this->queueName;
    }
}
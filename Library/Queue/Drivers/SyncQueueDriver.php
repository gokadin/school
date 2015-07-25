<?php

namespace Library\Queue\Drivers;

use Library\Queue\Job;

class SyncQueueDriver
{
    public function push(Job $job)
    {
        $job->handle();
    }
}
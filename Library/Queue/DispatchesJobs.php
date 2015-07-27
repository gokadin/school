<?php

namespace Library\Queue;

use Library\Facades\Queue as JobQueue;

trait DispatchesJobs
{
    public function dispatchJob($job)
    {
        if ($job instanceof ShouldQueue)
        {
            JobQueue::push($job);
            return;
        }

        JobQueue::pushSync($job);
    }
}
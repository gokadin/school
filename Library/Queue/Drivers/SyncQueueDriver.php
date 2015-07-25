<?php

namespace Library\Queue\Drivers;

use Library\Facades\Log;
use Library\Queue\Job;
use Exception;

class SyncQueueDriver
{
    public function push(Job $job)
    {
        try
        {
            $job->handle();
        }
        catch (Exception $e)
        {
            Log::error('Job '.get_class($job).' failed: '.$e->getMessage());
        }
    }
}
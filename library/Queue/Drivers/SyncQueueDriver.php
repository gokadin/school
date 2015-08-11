<?php

namespace Library\Queue\Drivers;

use App\Events\Event;
use Library\Facades\App;
use Library\Facades\Log;
use Exception;

class SyncQueueDriver
{
    public function push($job, $handler = null)
    {
        try
        {
            if ($job instanceof Event)
            {
                $this->executeEventListener($job, $handler);
                return;
            }

            $this->executeJob($job);
        }
        catch (Exception $e)
        {
            Log::error('Job '.get_class($job).' failed: '.$e->getMessage());
        }
    }

    protected function executeJob($job)
    {
        $parameters = App::container()->resolveMethodParameters($job, 'handle');
        call_user_func_array([$job, 'handle'], $parameters);
    }

    protected function executeEventListener($job, $handler)
    {
        $handler->handle($job);
    }
}
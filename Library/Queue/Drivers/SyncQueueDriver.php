<?php

namespace Library\Queue\Drivers;

use Library\Facades\App;
use Library\Facades\Log;
use Exception;

class SyncQueueDriver
{
    public function push($job)
    {
        try
        {
            $parameters = App::container()->resolveMethodParameters($job, 'handle');
            call_user_func_array([$job, 'handle'], $parameters);
        }
        catch (Exception $e)
        {
            Log::error('Job '.get_class($job).' failed: '.$e->getMessage());
        }
    }
}
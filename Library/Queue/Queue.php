<?php

namespace Library\Queue;

use Library\Facades\DB;

class Queue
{
    public function push(Job $job)
    {
        DB::exec('INSERT INTO '.env('QUEUE_TABLE').' (name, max_attempts, execution_date) VALUES('.
            get_class($job).', '.
            $job->getMaxAttempts().', '.
            $job->getExecutionDate());

        // .....
    }
}
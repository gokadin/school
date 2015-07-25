<?php

namespace Library\Queue;

use Library\Facades\DB;
use ReflectionClass;

class Queue
{
    public function push(Job $job)
    {
        $this->getSerializedArguments($job);
    }

    protected function getSerializedArguments(Job $job)
    {
        $r = new ReflectionClass($job);

        $parameters = $r->getConstructor()->getParameters();

        foreach ($parameters as $parameter)
        {
            
        }
    }

    protected function insertJob(Job $job)
    {
        DB::exec('INSERT INTO '.env('QUEUE_TABLE').' (name, max_attempts, execution_date) VALUES('.
            get_class($job).', '.
            $job->getMaxAttempts().', '.
            $job->getExecutionDate().')');
    }
}
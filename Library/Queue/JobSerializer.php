<?php

namespace Library\Queue;

use ReflectionClass;

class JobSerializer
{
    public function getSerializedJobArguments(Job $job)
    {
        $r = new ReflectionClass($job);

        $parameters = $r->getConstructor()->getParameters();

        $arguments = [];

        foreach ($parameters as $parameter)
        {
            //$arguments[]
        }

        echo 'serializedthing';
    }
}
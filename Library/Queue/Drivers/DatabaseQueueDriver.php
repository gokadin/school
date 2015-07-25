<?php

namespace Library\Queue\Drivers;

use Library\Facades\DB;
use Library\Queue\Job;
use Library\Queue\JobSerializer;

class DatabaseQueueDriver
{
    protected $queueTable;
    protected $failedTable;
    protected $jobSerializer;

    public function __construct($settings)
    {
        $this->queueTable = $settings['table'];
        $this->failedTable = $settings['failedTable'];
        $this->jobSerializer = new JobSerializer();

        $this->createTables();
    }

    public function push(Job $job)
    {
        $serializedArguments = $this->jobSerializer->getSerializedJobArguments($job);

        //$this->insertJobInDatabase($job, $serializedArguments);
    }

    protected function insertJobInDatabase($job, $arguments)
    {
        DB::exec('INSERT INTO '.$this->queueTable.' (name, max_attempts, execution_date) VALUES('.
            get_class($job).', '.
            $job->getMaxAttempts().', '.
            $job->getExecutionDate().', '.
            $arguments.')');
    }

    protected function createTables()
    {
        DB::exec('CREATE TABLE IF NOT EXISTS '.$this->queueTable.'('.
            'id INT(11) AUTO_INCREMENT PRIMARY KEY,'.
            'name VARCHAR(32) NOT NULL, '.
            'max_attempts INT(11) NOT NULL, '.
            'execution_date DATETIME NOT NULL, '.
            'arguments VARCHAR(255) NOT NULL'.
            ')');

        //DB::exec('CREATE TABLE IF NOT EXISTS '.$this->failedTable.'('.);
    }
}
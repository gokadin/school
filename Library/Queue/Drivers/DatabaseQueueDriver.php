<?php

namespace Library\Queue\Drivers;

use Carbon\Carbon;
use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;

class DatabaseQueueDriver
{
    protected $queueTable;
    protected $failedTable;

    public function __construct($settings)
    {
        $this->queueTable = $settings['table'];
        $this->failedTable = $settings['failedTable'];

        $this->createTables();
    }

    public function push($job)
    {
        $serializedData = serialize($job);

        $this->insertJobInDatabase($job, $serializedData);
    }

    protected function insertJobInDatabase($job, $data)
    {
        $executionDate = Carbon::now();
        $executionDate->addSeconds($job->delay);

        DB::exec('INSERT INTO '.$this->queueTable.' (max_attempts, execution_date, data) VALUES('.
            $job->maxAttempts.', '.
            '\''.$executionDate.'\', '.
            '\''.str_replace('\\', '\\\\', $data).'\')');
    }

    protected function createTables()
    {
        DB::exec('CREATE TABLE IF NOT EXISTS '.$this->queueTable.' ('.
            'id INT(11) AUTO_INCREMENT PRIMARY KEY, '.
            'max_attempts INT(11) NOT NULL, '.
            'execution_date DATETIME NOT NULL, '.
            'data TEXT NOT NULL'.
            ')');

        DB::exec('CREATE TABLE IF NOT EXISTS '.$this->failedTable.' ('.
            'id INT(11) AUTO_INCREMENT PRIMARY KEY, '.
            'data TEXT NOT NULL)');
    }
}
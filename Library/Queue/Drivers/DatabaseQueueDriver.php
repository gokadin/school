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
        $serializedData = json_encode([
            'className' => get_class($job),
            'data' => serialize($job)
        ]);

        $this->insertJobInDatabase($job, $serializedData);
    }

    protected function insertJobInDatabase($job, $data)
    {
        $executionDate = Carbon::now();
        $executionDate->addSeconds($job->delay);

        DB::exec('INSERT INTO '.$this->queueTable.' (name, max_attempts, execution_date, serializedData) VALUES('.
            '\''.str_replace('\\', '\\\\', get_class($job)).'\''.', '.
            $job->maxAttempts.', '.
            '\''.$executionDate.'\', '.
            '\''.$data.'\')');
    }

    protected function createTables()
    {
        DB::exec('CREATE TABLE IF NOT EXISTS '.$this->queueTable.'('.
            'id INT(11) AUTO_INCREMENT PRIMARY KEY,'.
            'name VARCHAR(255) NOT NULL, '.
            'max_attempts INT(11) NOT NULL, '.
            'execution_date DATETIME NOT NULL, '.
            'serializedData TEXT NOT NULL'.
            ')');

        //DB::exec('CREATE TABLE IF NOT EXISTS '.$this->failedTable.'('.);
    }
}
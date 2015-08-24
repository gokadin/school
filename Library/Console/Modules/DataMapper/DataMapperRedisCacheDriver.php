<?php

namespace Library\Console\Modules\DataMapper;

use Library\Database\Schema;
use Predis\Client as PredisClient;

class DataMapperRedisCacheDriver
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new PredisClient([
            'database' => 15
        ]);
    }

    public function loadSchema(Schema $schema)
    {
        foreach ($schema->tables() as $table)
        {
            $columnNames = [];
            foreach ($table->columns() as $column)
            {
                $columnNames[] = $column->getName();
            }

            $this->redis->sadd($table->name(), $columnNames);
            $this->redis->set($table->modelName().':table', $table->name());
        }
    }

    public function getTableByClass($class)
    {
        $tableName = $this->redis->get($class.':table');

        return $this->redis->smembers($tableName);
    }
}
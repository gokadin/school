<?php

namespace Library\Database\Drivers;

use Library\Database\Table;
use Predis\Client as PredisClient;

class RedisDatabaseDriver implements IDatabaseDriver
{
    const ID_PREFIX = 'nextId';
    const ID_ACCESSOR = 'id';

    protected $redis;

    public function __construct($settings)
    {
        $this->redis = new PredisClient([
            'database' => $settings['database']
        ]);
    }

    public function insert(Table $table, $dictionary)
    {
        $id = $this->getNextId($table->name());

        $primaryKey = $table->getPrimaryKey();
        if (!is_null($primaryKey))
        {
            $dictionary[$primaryKey->getName()] = $id;
        }

        $this->redis->hmset($table->name().':'.self::ID_ACCESSOR.':'.$id, $dictionary);

        $this->insertIndexedColumns($table, $dictionary, $id);
    }

    public function select($tableName)
    {
        return $this->redis->hgetall($tableName);

        // ...
    }

    public function dropAll()
    {
        $this->redis->flushdb();
    }

    protected function getNextId($key)
    {
        return $this->redis->incr(self::ID_PREFIX.':'.$key);
    }

    protected function insertIndexedColumns(Table $table, $dictionary, $id)
    {
        foreach ($table->columns() as $column)
        {
            if ($column->hasIndex())
            {
                $this->redis->sadd($table->name().':'.$column->getName().':'.$dictionary[$column->getName()], $id);
            }
        }
    }
}
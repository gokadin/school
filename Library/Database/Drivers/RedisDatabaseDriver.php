<?php

namespace Library\Database\Drivers;

use Library\Database\Table;
use Predis\Client as PredisClient;

class RedisDatabaseDriver implements IDatabaseDriver
{
    const ID_PREFIX = 'nextId';
    const ID_ACCESSOR = 'id';
    const SCHEMA_PREFIX = 'SCHEMA';

    protected $redis;
    protected $table;

    public function __construct($settings)
    {
        $this->redis = new PredisClient([
            'database' => $settings['database']
        ]);
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function create(Table $table)
    {
        $columnNames = [];
        foreach ($table->columns() as $column)
        {
            $columnNames[] = $column->getName();
        }

        $this->redis->sadd(self::SCHEMA_PREFIX.':'.$table->name().':columns', $columnNames);

        foreach ($table->columns() as $column)
        {
            $this->redis->hmset(self::SCHEMA_PREFIX.':'.$table->name().':column:'.$column->getName(), [
                'isPrimaryKey' => $column->isPrimaryKey(),
                'type' => $column->getType(),
                'size' => $column->getSize(),
                'precision' => $column->getPrecision(),
                'isNullable' => $column->isNullable(),
                'isRequired' => $column->isRequired(),
                'isUnique' => $column->isUnique(),
                'isDefault' => $column->isDefault(),
                'defaultValue' => $column->getDefault()
            ]);
        }
    }

    public function insert(array $data)
    {
        $id = $this->getNextId($this->table);

        $this->redis->hmset($this->table.':'.self::ID_ACCESSOR.':'.$id, $data);

        $this->insertIndexedColumns($table, $dictionary, $id);
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
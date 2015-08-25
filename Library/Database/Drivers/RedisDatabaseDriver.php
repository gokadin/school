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
                'isNullable' => $column->isNullable() ? 1 : 0,
                'isRequired' => $column->isRequired() ? 1 : 0,
                'isUnique' => $column->isUnique() ? 1 : 0,
                'isDefault' => $column->isDefault() ? 1 : 0,
                'defaultValue' => $column->getDefault(),
                'hasIndex' => $column->hasIndex() ? 1 : 0
            ]);
        }
    }

    protected function accessTable($table)
    {
        $t = new Table($table);

        $columnNames = $this->redis->smembers(self::SCHEMA_PREFIX.':'.$table.':columns');

        foreach ($columnNames as $columnName)
        {
            $columnProperties = $this->redis->hgetall(self::SCHEMA_PREFIX.':'.$table.':column:'.$columnName);

            if ($columnProperties['isPrimaryKey'])
            {
                $t->increments($columnName);
                continue;
            }

            $column = null;
            switch ($columnProperties['type'])
            {
                case 'integer':
                    $t->integer($columnName);
                    break;
                case 'string':
                    $t->string($columnName);
                    break;
                case 'text':
                    $t->text($columnName);
                    break;
                case 'decimal':
                    $t->decimal($columnName);
                    break;
                case 'boolean':
                    $t->boolean($columnName);
                    break;
                case 'datetime':
                    $t->datetime($columnName);
                    break;
            }

            if (is_null($column))
            {
                continue;
            }

            $column = $column->size($columnProperties['size']);
            $column = $column->precision($columnProperties['precision']);
            if ($columnProperties['isNullable'] == 1)
            {
                $column = $column->nullable();
            }
            if ($columnProperties['isDefault'] == 1)
            {
                $column = $column->default($columnProperties['defaultValue']);
            }
            if ($columnProperties['isUnique'] == 1)
            {
                $column = $column->unique();
            }
            if ($columnProperties['hasIndex'] == 1)
            {
                $column->addIndex();
            }
        }

        return $t;
    }

    public function insert(array $data)
    {
        $id = $this->getNextId($this->table);

        $this->redis->hmset($this->table.':'.self::ID_ACCESSOR.':'.$id, $data);

        $this->insertIndexedColumns($this->accessTable($this->table), $data, $id);

        $this->clean();
    }

    public function dropAll()
    {
        $this->redis->flushdb();
    }

    protected function getNextId($key)
    {
        return $this->redis->incr(self::ID_PREFIX.':'.$key);
    }

    protected function insertIndexedColumns($table, $data, $id)
    {
        foreach ($table->columns() as $column)
        {
            if ($column->hasIndex())
            {
                $this->redis->sadd($table->name().':'.$column->getName().':'.$dictionary[$column->getName()], $id);
            }
        }
    }

    protected function clean()
    {
        $this->table = null;
    }

    function select(array $data)
    {
        // TODO: Implement select() method.
    }

    function update(array $data)
    {
        // TODO: Implement update() method.
    }

    function delete()
    {
        // TODO: Implement delete() method.
    }

    function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    function commit()
    {
        // TODO: Implement commit() method.
    }

    function rollBack()
    {
        // TODO: Implement rollBack() method.
    }
}
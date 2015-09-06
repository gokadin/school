<?php

namespace Library\Console\Modules\DataMapper;

use Library\Database\Schema;
use Predis\Client as PredisClient;

class DataMapperRedisCacheDriver
{
    const SCHEMA = '_SCHEMA';

    protected $redis;

    public function __construct($database)
    {
        $this->redis = new PredisClient([
            'database' => $database
        ]);
    }

    public function loadSchema(Schema $schema)
    {
        foreach ($schema->tables() as $table)
        {
            $this->redis->set($table->modelName().':table', $table->name());

            $columnNames = [];
            foreach ($table->columns() as $column)
            {
                $columnNames[] = $column->getName();
            }

            $this->redis->sadd(self::SCHEMA.':'.$table->name().':columns', $columnNames);

            foreach ($table->columns() as $column)
            {
                $this->redis->hmset(self::SCHEMA.':'.$table->name().':column:'.$column->getName(), [
                    'isPrimaryKey' => $column->isPrimaryKey() ? 1 : 0,
                    'type' => $column->getType(),
                    'size' => $column->getSize(),
                    'precision' => $column->getPrecision(),
                    'isNullable' => $column->isNullable() ? 1 : 0,
                    'isRequired' => $column->isRequired() ? 1 : 0,
                    'isUnique' => $column->isUnique() ? 1 : 0,
                    'isDefault' => $column->isDefault() ? 1 : 0,
                    'defaultValue' => $column->getDefault(),
                    'hasIndex' => $column->hasIndex() ? 1 : 0,
                    'propertyName' => $column->getPropertyName()
                ]);
            }
        }
    }

    public function getTableByClass($class)
    {
        $tableName = $this->redis->get($class.':table');

        return $this->getTable($tableName);
    }

    protected function getTable($table)
    {
        $t = new Table($table);

        $columnNames = $this->redis->smembers(self::SCHEMA.':'.$table.':columns');

        foreach ($columnNames as $columnName)
        {
            $columnProperties = $this->redis->hgetall(self::SCHEMA.':'.$table.':column:'.$columnName);

            if ($columnProperties['isPrimaryKey'])
            {
                $t->increments($columnName);
                continue;
            }

            $column = null;
            switch ($columnProperties['type'])
            {
                case 'integer':
                    $column = $t->integer($columnName);
                    break;
                case 'string':
                    $column = $t->string($columnName);
                    break;
                case 'text':
                    $column = $t->text($columnName);
                    break;
                case 'decimal':
                    $column = $t->decimal($columnName);
                    break;
                case 'boolean':
                    $column = $t->boolean($columnName);
                    break;
                case 'datetime':
                    $column = $t->datetime($columnName);
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

            $column->propertyName($columnProperties['propertyName']);
        }

        return $t;
    }
}
<?php

namespace Library\DataMapper\Database;

use Library\DataMapper\Database\Drivers\MySqlDriver;

class QueryBuilder
{
    protected $databaseDriver;
    protected $table;
    protected $wheres = [];

    public function __construct($config)
    {
        $this->initializeDatabaseDriver($config);
    }

    protected function initializeDatabaseDriver($config)
    {
        switch ($config['databaseDriver'])
        {
            default:
                $this->databaseDriver = new MySqlDriver($config[$config['databaseDriver']]);
                break;
        }
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select(array $fields = ['*'])
    {
        $result = [];

        switch ($this->databaseDriver->name())
        {
            case 'mysql':
                $result = $this->selectMySql($fields);
                break;
        }

        $this->clear();

        return $result;
    }

    public function where($field, $operator, $value)
    {
        $this->wheres[] = [$field, $operator, $value, 'AND'];
        return $this;
    }

    public function orWhere($field, $operator, $value)
    {
        $this->wheres[] = [$field, $operator, $value, 'OR'];
        return $this;
    }

    protected function selectMySql($fields)
    {
        $str = '';

        $wheres = $this->databaseDriver->buildWheres($this->wheres);

        $str = 'SELECT ';
        $str .= implode(', ', $fields);
        $str .= ' FROM '.$this->table;
        $str .= ' '.$wheres;

        return $this->databaseDriver->execute($str);
    }

    public function insert(array $data)
    {
        // switch case driver...

        $str = 'INSERT INTO '.$this->table;
        $str .= ' ('.implode(',', array_keys($data)).')';

        $processed = [];
        foreach ($data as $key => $value)
        {
            $processed[':'.$key] = $value;
        }
        $data = $processed;

        $str .= ' VALUES('.implode(',', array_keys($data)).')';

        return $this->databaseDriver->insert($str, $data);
    }

    protected function clear()
    {
        $this->table = null;
        $this->wheres = [];
    }
}
<?php

namespace Library\DataMapper\Database;

use Library\DataMapper\Database\Drivers\MySqlDriver;

class QueryBuilder
{
    protected $databaseDriver;
    protected $command;
    protected $table;
    protected $wheres = [];
    protected $fields = [];

    public function __construct($config)
    {
        $this->initializeDatabaseDriver($config);
    }

    protected function initializeDatabaseDriver($config)
    {
        switch ($config['driver'])
        {
            default:
                $this->databaseDriver = new MySqlDriver($config);
                break;
        }
    }

    public function select(array $fields = ['*'])
    {
        $this->command = 'select';
        $this->fields = $fields;
        return $this;
    }

    public function from($table)
    {
        $this->table = $table;
        return $this;
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

    public function execute()
    {
        $result = [];

        switch ($this->databaseDriver->name())
        {
            case 'mysql':
                $result = $this->executeMySql();
                break;
        }

        $this->clear();

        return $result;
    }

    protected function executeMySql()
    {
        $str = '';

        $wheres = $this->databaseDriver->buildWheres($this->wheres);

        switch ($this->command)
        {
            case 'select':
                $str = 'SELECT ';
                $str .= implode(', ', $this->fields);
                $str .= ' FROM '.$this->table;
                $str .= ' '.$wheres;
                break;
        }

        return $this->databaseDriver->execute($str);
    }

    protected function clear()
    {
        $this->command = null;
        $this->table = null;
        $this->wheres = [];
        $this->fields = [];
    }
}
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
        $this->addWhere($field, $operator, $value, 'AND');
        return $this;
    }

    public function orWhere($field, $operator, $value)
    {
        $this->addWhere($field, $operator, $value, 'OR');
        return $this;
    }

    protected function addWhere($field, $operator, $value, $link)
    {
        $this->wheres[] = [
            'var' => $field,
            'operator' => $operator,
            'value' => $value,
            'link' => $link
        ];
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

        $this->clear();

        return $this->databaseDriver->insert($str, $data);
    }

    public function update(array $data)
    {
        // switch case driver...

        $str = 'UPDATE '.$this->table.' SET ';

        $processed = [];
        $i = 0;
        foreach ($data as $key => $value)
        {
            if ($i > 0)
            {
                $str .= ', ';
            }

            if (is_null($value))
            {
                $value = '';
            }

            $str .= $key.' = :'.$key;
            $processed[':'.$key] = $value;
            $i++;
        }
        $data = $processed;

        $this->clear();

        $this->databaseDriver->update($str, $data);
    }

    public function delete()
    {
        $str = 'DELETE FROM '.$this->table;
        $str .= $this->databaseDriver->buildWheres($this->wheres);

        $this->clear();

        return $this->databaseDriver->delete($str);
    }

    protected function clear()
    {
        $this->table = null;
        $this->wheres = [];
    }
}
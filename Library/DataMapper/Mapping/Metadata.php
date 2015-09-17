<?php

namespace Library\DataMapper\Mapping;

use ReflectionClass;

class Metadata
{
    protected $columns = [];
    protected $table;
    protected $reflectionClass;
    protected $primaryKey;

    public function __construct($table, ReflectionClass $r)
    {
        $this->table = $table;
        $this->reflectionClass = $r;
    }

    public function table()
    {
        return $this->table;
    }

    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    public function primaryKey()
    {
        return $this->primaryKey;
    }

    public function columns()
    {
        return $this->columns;
    }

    public function getColumn($name)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }

    public function addColumn(Column $column)
    {
        $this->columns[$column->name()] = $column;

        if ($column->isPrimaryKey())
        {
            $this->primaryKey = $column;
        }
    }
}
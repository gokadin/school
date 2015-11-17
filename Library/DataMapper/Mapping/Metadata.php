<?php

namespace Library\DataMapper\Mapping;

use ReflectionClass;

class Metadata
{
    const ASSOC_HAS_MANY = 'HasMany';
    const ASSOC_BELONGS_TO = 'BelongsTo';

    protected $columns = [];
    protected $associations = [];
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

    public function associations()
    {
        return $this->associations;
    }

    public function getAssociation($fieldName)
    {
        return $this->associations[$fieldName];
    }

    public function addAssociation($data)
    {
        switch ($data['type'])
        {
            case Metadata::ASSOC_HAS_MANY:
                $this->associations[$data['fieldName']] = [
                    'type' => $data['type'],
                    'target' => $data['target'],
                    'mappedBy' => $data['mappedBy']
                ];
                break;
            case Metadata::ASSOC_BELONGS_TO:
                $this->associations[$data['fieldName']] = [
                    'type' => $data['type'],
                    'target' => $data['target'],
                ];
                break;
        }
    }

    public function generateForeignKeyName()
    {
        return lcfirst($this->reflectionClass->getShortName()).'_id';
    }
}
<?php

namespace Library\DataMapper\Mapping;

use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use ReflectionClass;

class Metadata
{
    const ASSOC_HAS_MANY = 'HasMany';
    const ASSOC_HAS_ONE = 'HasOne';
    const ASSOC_BELONGS_TO = 'BelongsTo';

    protected $columns = [];
    protected $associations = [];
    protected $class;
    protected $table;
    protected $reflectionClass;
    protected $primaryKey;
    protected $createdAt;
    protected $updatedAt;

    public function __construct($class, $table, ReflectionClass $r)
    {
        $this->class = $class;
        $this->table = $table;
        $this->reflectionClass = $r;
    }

    public function className()
    {
        return $this->class;
    }

    public function table()
    {
        return $this->table;
    }

    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    public function reflProp($propName)
    {
        $prop = $this->reflectionClass->getProperty($propName);
        $prop->setAccessible(true);
        return $prop;
    }

    public function primaryKey()
    {
        return $this->primaryKey;
    }

    public function hasCreatedAt()
    {
        return !is_null($this->createdAt);
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function hasUpdatedAt()
    {
        return !is_null($this->updatedAt);
    }

    public function updatedAt()
    {
        return $this->updatedAt;
    }

    public function columns()
    {
        return $this->columns;
    }

    public function getColumn($name)
    {
        return isset($this->columns[$name]) ? $this->columns[$name] : null;
    }

    public function getColumnByPropName($propName)
    {
        foreach ($this->columns as $column)
        {
            if ($column->propName() == $propName)
            {
                return $column;
            }
        }

        return null;
    }

    public function addColumn(Column $column)
    {
        $this->columns[$column->name()] = $column;

        if ($column->isPrimaryKey())
        {
            $this->primaryKey = $column;
        }

        if ($column->isCreatedAt())
        {
            $this->createdAt = $column;
        }

        if ($column->isUpdatedAt())
        {
            $this->updatedAt = $column;
        }
    }

    public function associations()
    {
        return $this->associations;
    }

    public function getAssociation($propName)
    {
        return $this->associations[$propName];
    }

    public function addHasOneAssociation($columnName, $propName, $target, $nullable)
    {
        $column = new Column(lcfirst($columnName).'_id', $propName, 'integer', AnnotationDriver::DEFAULT_INTEGER_SIZE);
        $column->setForeignKey();
        $column->setNullable();
        $this->columns[$columnName] = $column;

        $this->associations[$propName] = [
            'column' => $column,
            'type' => self::ASSOC_HAS_ONE,
            'target' => $target,
            'isNullable' => $nullable
        ];
    }

    public function addAssociation($data)
    {
        switch ($data['type'])
        {
            case self::ASSOC_HAS_MANY:
                $this->associations[$data['propName']] = [
                    'type' => $data['type'],
                    'target' => $data['target'],
                    'mappedBy' => $data['mappedBy']
                ];
                break;
            case self::ASSOC_BELONGS_TO:
                $this->associations[$data['propName']] = [
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
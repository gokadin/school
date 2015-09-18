<?php

namespace Library\DataMapper;

use IteratorAggregate;
use JsonSerializable;

class EntityCollection implements IteratorAggregate, JsonSerializable
{
    protected $items = [];
    protected $count;
    protected $changed;

    public function __construct(array $entities = [])
    {
        $this->count = 0;
        $this->changed = false;
        $this->add($entities);
    }

    public function add($oneOrManyEntities)
    {
        is_array($oneOrManyEntities)
            ? $this->addMany($oneOrManyEntities)
            : $this->addOne($oneOrManyEntities);

        $this->changed = true;
    }

    protected function addOne($entity)
    {
        $this->items[] = [
            'entity' => $entity,
            'changed' => false
        ];

        $this->count++;
    }

    protected function addMany(array $entities)
    {
        foreach ($entities as $entity)
        {
            $this->items[] = [
                'entity' => $entity,
                'changed' => false
            ];

            $this->count++;
        }
    }

    public function first()
    {
        if ($this->count == 0)
        {
            return null;
        }

        return $this->items[0]['entity'];
    }

    public function last()
    {
        if ($this->count == 0)
        {
            return null;
        }

        return $this->items[$this->count - 1]['entity'];
    }

    public function elementAt($index)
    {
        if ($this->count <= $index)
        {
            return null;
        }

        return $this->items[$index]['entity'];
    }

    public function count()
    {
        return $this->count;
    }

    public function isEmpty()
    {
        return $this->count == 0;
    }

    public function ignoreChanges()
    {
        $this->changed = false;
    }

    protected function getEntityArray()
    {
        $entities = [];
        foreach ($this->items as $item)
        {
            $entities[] = $item['entity'];
        }

        return $entities;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->getEntityArray());
    }

    function jsonSerialize()
    {
        return $this->getEntityArray();
    }
}
<?php

namespace Library\DataMapper\Collection;

class EntityCollection extends AbstractEntityCollection
{
    protected $items = [];

    public function __construct(array $entities = [])
    {
        parent::__construct();

        $this->add($entities);
    }

    public function add($value)
    {
        is_array($value)
            ? $this->addMany($value)
            : $this->addOne($value);
    }

    protected function addOne($entity)
    {
        $this->items[] = $entity;
        $this->count++;
    }

    protected function addMany(array $entities)
    {
        foreach ($entities as $entity)
        {
            $this->items[] = $entity;
            $this->count++;
        }
    }

    public function first()
    {
        return reset($this->items);
    }

    public function last()
    {
        return end($this->items);
    }

    public function at($index)
    {
        if ($this->count <= $index)
        {
            return null;
        }

        return $this->items[$index];
    }

    public function slice($offset, $length = null)
    {
        $slice = [];

        if (is_null($length))
        {
            for ($i = 0; $i < $this->count; $i++)
            {
                if ($i < $offset)
                {
                    continue;
                }

                $slice[] = $this->items[$i];
            }
        }
        else
        {
            $lengthCounter = 0;
            for ($i = 0; $i < $this->count; $i++)
            {
                if ($i < $offset)
                {
                    continue;
                }

                $slice[] = $this->items[$i];

                $lengthCounter++;
                if ($lengthCounter >= $length)
                {
                    break;
                }
            }
        }

        return new EntityCollection($slice);
    }

    public function toArray()
    {
        return $this->items;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    function jsonSerialize()
    {
        return $this->items;
    }
}
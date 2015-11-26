<?php

namespace Library\DataMapper\Collection;

use Library\DataMapper\DataMapper;
use SplObjectStorage;
use ArrayIterator;
use Exception;

class PersistentCollection extends AbstractEntityCollection
{
    protected $items;
    protected $addedItems;
    protected $removedItems;
    protected $dm;
    protected $class;
    protected $metadata;
    protected $isChanged;

    public function __construct(DataMapper $dm, $class, array $items = [])
    {
        parent::__construct();

        $this->items = $items;
        $this->count = sizeof($this->items);
        $this->addedItems = new SplObjectStorage();
        $this->removedItems = new SplObjectStorage();
        $this->dm = $dm;
        $this->class = $class;
        $this->metadata = $this->dm->getMetadata($class);
        $this->isChanged = false;
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
        $this->markAdded($entity);

        $this->count++;
    }

    protected function addMany($entities)
    {
        foreach ($entities as $entity)
        {
            $this->addOne($entity);
        }
    }

    public function remove($value)
    {
        is_array($value)
            ? $this->removeMany($value)
            : $this->removeOne($value);
    }

    protected function removeOne($entity)
    {
        foreach ($this->items as $key => $item)
        {
            if ($item === $entity)
            {
                unset($this->items[$key]);
                break;
            }
        }

        $this->markRemoved($entity);

        $this->count--;
    }

    protected function removeMany($entities)
    {
        foreach ($entities as $entity)
        {
            $this->removeOne($entity);
        }
    }

    public function isChanged()
    {
        return $this->isChanged;
    }

    public function addedItems()
    {
        return $this->addedItems;
    }

    public function removedItems()
    {
        return $this->removedItems;
    }

    protected function markAdded($entity)
    {
        if ($this->removedItems->contains($entity))
        {
            $this->removedItems->detach($entity);
            return;
        }

        $this->addedItems->attach($entity);

        $this->isChanged = true;
    }

    protected function markRemoved($entity)
    {
        if ($this->addedItems->contains($entity))
        {
            $this->addedItems->detach($entity);
            return;
        }

        $this->removedItems->attach($entity);

        $this->isChanged = true;
    }

    public function resetState()
    {
        $this->isChanged = false;
        $this->addedItems = new SplObjectStorage();
        $this->removedItems = new SplObjectStorage();
    }

    public function first()
    {
        return $this->loadIndex(0);
    }

    public function last()
    {
        return $this->loadIndex($this->count - 1);
    }

    public function at($index)
    {
        return $this->loadIndex($index);
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

        return new PersistentCollection($slice);
    }

    public function sortBy($property, $ascending = true)
    {
        $column = $this->metadata->getColumn($property);
        if (is_null($column))
        {
            throw new Exception('PersistentCollection.sortBy : invalid property '.$property);
        }

        $fieldName = $column->fieldName();
        $primaryKey = $this->metadata->primaryKey();
        $r = $this->metadata->getReflectionClass();
        $primaryKeyProperty = $r->getProperty($primaryKey->name());
        $primaryKeyProperty->setAccessible(true);

        $ids = [];
        foreach ($this->items as $item)
        {
            if (is_object($item))
            {
                $ids[$primaryKeyProperty->getValue($item)] = $item;
                continue;
            }

            $ids[$item] = null;
        }

        $sortedIds = $this->dm->queryBuilder()->table($this->metadata->table())
            ->where($primaryKey->fieldName(), 'in', '('.implode(',', array_keys($ids)).')')
            ->sortBy($fieldName, $ascending)
            ->select([$primaryKey->fieldName()]);

        $this->items = [];
        foreach ($sortedIds as $sortedId)
        {
            $object = $ids[$sortedId];

            if (is_null($object))
            {
                $this->items[] = $sortedId;
                continue;
            }

            $this->items[] = $object;
        }

        return $this;
    }

    public function getIdList()
    {
        $primaryKey = $this->metadata->primaryKey();
        $r = $this->metadata->getReflectionClass();
        $primaryKeyProperty = $r->getProperty($primaryKey->name());
        $primaryKeyProperty->setAccessible(true);
        $ids = [];

        foreach ($this->items as $item)
        {
            if (is_object($item))
            {
                $ids[] = $primaryKeyProperty->getValue($item);
                continue;
            }

            $ids[] = $item;
        }

        return $ids;
    }

    public function toArray()
    {
        $this->loadAll();

        return $this->items;
    }

    public function getIterator()
    {
        $this->loadAll();
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize()
    {
        $this->loadAll();

        return $this->items;
    }

    protected function loadAll()
    {
        $ids = [];
        foreach ($this->items as $index => $item)
        {
            if ($this->isLoaded($item))
            {
                continue;
            }

            $ids[] = $item;
            unset($this->items[$index]);
        }

        if (sizeof($ids) == 0)
        {
            return;
        }

        $entityCollection = $this->dm->findIn($this->class, $ids);

        foreach ($entityCollection as $entity)
        {
            $this->items[] = $entity;
        }
    }

    protected function loadIndex($index)
    {
        if ($this->count <= $index)
        {
            return null;
        }

        $item = $this->items[$index];
        if ($this->isLoaded($item))
        {
            return $item;
        }

        $this->items[$index] = $this->dm->find($this->class, $item);
        return $this->items[$index];
    }

    protected function isLoaded($item)
    {
        return is_object($item);
    }
}
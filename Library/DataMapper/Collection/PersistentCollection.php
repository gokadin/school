<?php

namespace Library\DataMapper\Collection;

use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\DataMapper;
use SplObjectStorage;
use ArrayIterator;
use Exception;

/*
 * 1) Should never instantiate this class.
 * 2) When adding an entity, if it is not already persisted,
 *    it will not be usable in any collection operations, not even count().
 */
final class PersistentCollection extends AbstractEntityCollection
{
    protected $dm;
    protected $class;
    protected $metadata;
    protected $primaryKeyColumn;
    protected $primaryKeyProperty;

    protected $items;
    protected $subset;
    protected $useSubset;
    protected $subsetCount;
    protected $addedItems;
    protected $removedItems;
    protected $isChanged;

    protected $sortingRules = [];
    protected $wheres = [];

    public function __construct(DataMapper $dm, $class, array $items = [])
    {
        parent::__construct();

        $this->dm = $dm;
        $this->class = $class;
        $this->metadata = $this->dm->getMetadata($class);
        $this->primaryKeyColumn = $this->metadata->primaryKey();
        $this->primaryKeyProperty = null;

        $this->items = $items;
        $this->count = sizeof($this->items);
        $this->addedItems = new SplObjectStorage();
        $this->removedItems = new SplObjectStorage();
        $this->isChanged = false;
        $this->useSubset = false;
    }

    public function add($value)
    {
        is_array($value)
            ? $this->addMany($value)
            : $this->addOne($value);
    }

    protected function addOne($entity)
    {
        $id = $this->getEntityId($entity);
        $this->markAdded($entity, $id);

        if (is_null($id))
        {
            return;
        }

        $this->items[] = $entity;

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
        $id = $this->getEntityId($entity);

        unset($this->items[$id]);

        $this->markRemoved($entity, $id);

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

    protected function getEntityId($entity)
    {
        if (is_null($this->primaryKeyProperty))
        {
            $this->primaryKeyProperty = $this->metadata->getReflectionClass()
                ->getProperty($this->primaryKeyColumn->name());
            $this->primaryKeyProperty->setAccessible(true);

        }

        return $this->primaryKeyProperty->getValue($entity);
    }

    public function addedItems()
    {
        return $this->addedItems;
    }

    public function removedItems()
    {
        return $this->removedItems;
    }

    protected function markAdded($entity, $id)
    {
        if ($this->removedItems->contains($entity))
        {
            $this->removedItems->detach($entity);
            return;
        }

        $this->addedItems->attach($entity, $id);

        $this->isChanged = true;
    }

    protected function markRemoved($entity, $id)
    {
        if ($this->addedItems->contains($entity))
        {
            $this->addedItems->detach($entity);
            return;
        }

        $this->removedItems->attach($entity, $id);

        $this->isChanged = true;
    }

    public function resetState()
    {
        $this->sortingRules = [];
        $this->wheres = [];
    }

    public function count()
    {
        $this->execute();

        return $this->useSubset ? $this->subsetCount : $this->count;
    }

    public function first()
    {
        $this->execute();

        return $this->loadIndex(0);
    }

    public function last()
    {
        $this->execute();

        return $this->loadIndex($this->count - 1);
    }

    public function at($index)
    {
        $this->execute();

        return $this->loadIndex($index);
    }

    public function slice($offset, $length = null)
    {
        $this->execute();

        $slice = [];
        $items = $this->items;
        $count = $this->count;
        if ($this->useSubset)
        {
            $items = $this->subset;
            $count = $this->subsetCount;
        }

        if (is_null($length))
        {
            for ($i = 0; $i < $count; $i++)
            {
                if ($i < $offset)
                {
                    continue;
                }

                $slice[] = $items[$i];
            }

            $this->loadArray($slice);
            return $slice;
        }

        $lengthCounter = 0;
        for ($i = 0; $i < $count; $i++)
        {
            if ($i < $offset)
            {
                continue;
            }

            $slice[] = $items[$i];

            $lengthCounter++;
            if ($lengthCounter >= $length)
            {
                break;
            }
        }

        $this->loadArray($slice);
        return $slice;
    }

    public function where($var, $operator, $value = null)
    {
        $this->addWhere($var, $operator, $value, 'AND');

        return $this;
    }

    public function orWhere($var, $operator, $value = null)
    {
        $this->addWhere($var, $operator, $value, 'OR');

        return $this;
    }

    protected function addWhere($var, $operator, $value = null, $link = 'AND')
    {
        if (is_null($value))
        {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = ['var' => $var, 'operator' => $operator, 'value' => $value, 'link' => $link];
    }

    public function sortBy($property, $ascending = true)
    {
        $this->sortingRules[] = [
            'sortBy' => $property,
            'ascending' => $ascending
        ];

        return $this;
    }

    public function toArray()
    {
        $this->execute();

        return array_values($this->loadAll());
    }

    public function toIdMap()
    {
        $this->execute();

        return $this->loadAll();
    }

    public function getIdList()
    {
        $this->execute();

        return $this->useSubset
            ? array_keys($this->subset)
            : array_keys($this->items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    protected function loadAll()
    {
        $items = $this->useSubset ? $this->subset : $this->items;
        $ids = [];
        foreach ($items as $id => $item)
        {
            if (!is_null($item))
            {
                continue;
            }

            $ids[] = $id;
        }

        if (sizeof($ids) == 0)
        {
            return;
        }

        if ($this->useSubset)
        {
            foreach ($this->dm->findIn($this->class, $ids) as $entity)
            {
                $id = $this->getEntityId($entity);
                $this->subset[$id] = $entity;
                $this->items[$id] = $entity;
            }

            return $this->subset;
        }

        foreach ($this->dm->findIn($this->class, $ids) as $entity)
        {
            $this->items[$this->getEntityId($entity)] = $entity;
        }

        return $this->items;
    }

    protected function loadArray(array &$items)
    {
        $ids = [];
        foreach ($items as $id => $item)
        {
            if (!is_null($item))
            {
                continue;
            }

            $ids[] = $id;
        }

        if (sizeof($ids) == 0)
        {
            return;
        }

        foreach ($this->dm->findIn($this->class, $ids) as $entity)
        {
            $items[$this->getEntityId($entity)] = $entity;
        }
    }

    protected function loadIndex($index)
    {
        $count = $this->useSubset ? $this->subsetCount : $this->count;

        if ($count - 1 < $index || $index > $count - 1)
        {
            return null;
        }

        $i = 0;
        foreach ($this->items as $id => &$entity)
        {
            if ($i == $index)
            {
                if (is_null($entity))
                {
                    $entity = $this->dm->find($this->class, $id);
                    if ($this->useSubset)
                    {
                        $this->subset[$id] = $entity;
                    }

                    return  $entity;
                }

                return $entity;
            }

            $i++;
        }
    }

    protected function execute()
    {
        if (sizeof($this->wheres) == 0 && sizeof($this->sortingRules) == 0)
        {
            $this->useSubset = false;
            return;
        }

        $this->useSubset = true;

        $queryBuilder = $this->dm->queryBuilder()->table($this->metadata->table())
            ->where($this->metadata->primaryKey()->fieldName(), 'in', '('.implode(',', array_keys($this->items)).')');

        $this->executeWheres($queryBuilder);

        $this->executeSortingRules($queryBuilder);

        $ids = $queryBuilder->select([$this->metadata->primaryKey()->fieldName()]);

        $this->buildSubset($ids);
    }

    protected function executeWheres(QueryBuilder &$queryBuilder)
    {
        if (sizeof($this->wheres) == 0)
        {
            return;
        }

        foreach ($this->wheres as $where)
        {
            $where['link'] == 'AND'
                ? $queryBuilder->where($where['var'], $where['operator'], $where['value'])
                : $queryBuilder->orWhere($where['var'], $where['operator'], $where['value']);
        }
    }

    protected function executeSortingRules(QueryBuilder &$queryBuilder)
    {
        if (sizeof($this->sortingRules) == 0)
        {
            return;
        }

        foreach ($this->sortingRules as $rule)
        {
            $column = $this->metadata->getColumn($rule['sortBy']);
            if (is_null($column))
            {
                throw new Exception('PersistentCollection.sortBy : invalid property '.$rule['sortBy']);
            }

            $queryBuilder->sortBy($column->fieldName(), $rule['ascending']);
        }
    }

    protected function buildSubset($ids)
    {
        $this->subset = [];
        $this->subsetCount = 0;
        foreach ($ids as $id)
        {
            $this->subset[$id] = $this->items[$id];
            $this->subsetCount++;
        }
    }
}
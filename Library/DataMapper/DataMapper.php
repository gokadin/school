<?php

namespace Library\DataMapper;

use Carbon\Carbon;
use Library\DataMapper\Collection\EntityCollection;
use Library\DataMapper\Collection\PersistentCollection;
use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\Mapping\Column;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use Library\DataMapper\Mapping\Metadata;
use Symfony\Component\Yaml\Exception\RuntimeException;
use ReflectionClass;
use ReflectionException;
use SplObjectStorage;

class DataMapper
{
    protected $mappingDriver;
    protected $queryBuilder;
    protected $loadedMetadata = [];
    protected $loadedEntities = [];

    public function __construct($config)
    {
        $this->initializeMappingDriver($config['mappingDriver']);

        $this->queryBuilder = new QueryBuilder($config);
    }

    protected function initializeMappingDriver($driverName)
    {
        switch ($driverName)
        {
            default:
                $this->mappingDriver = new AnnotationDriver();
                break;
        }
    }

    public function beginTransaction()
    {
        $this->queryBuilder->beginTransaction();
    }

    public function rollBack()
    {
        $this->queryBuilder->rollBack();
    }

    public function commit()
    {
        $this->queryBuilder->commit();
    }

    public function find($class, $id)
    {
        $loadedObject = $this->findLoadedEntity($class, $id);
        if (!is_null($loadedObject))
        {
            return $loadedObject;
        }

        $metadata = $this->getMetadata($class);

        $data = $this->queryBuilder()->table($metadata->table())
            ->where($metadata->primaryKey()->name(), '=', $id)
            ->select();

        if (sizeof($data) == 0)
        {
            return null;
        }

        return $this->buildEntity($metadata, $data[0]);
    }

    public function findOrFail($class, $id)
    {
        $entity = $this->find($class, $id);

        if (!is_null($entity))
        {
            return $entity;
        }

        throw new RuntimeException('Could not find entity '.$class.' with id '.$id);
    }

    public function findIn($class, array $ids)
    {
        $metadata = $this->getMetadata($class);

        $results = $this->queryBuilder()->table($metadata->table())
            ->where($metadata->primaryKey()->fieldName(), 'in', '('.implode(',', $ids).')')
            ->select();

        $collection = new EntityCollection();
        foreach ($results as $data)
        {
            $collection->add($this->buildEntity($metadata, $data));
        }

        return $collection;
    }

    public function findBy($class, array $conditions)
    {
        $metadata = $this->getMetadata($class);

        $this->queryBuilder->table($metadata->table());

        foreach ($conditions as $var => $value)
        {
            $this->queryBuilder->where($var, '=', $value);
        }

        $results = $this->queryBuilder->select();

        $collection = new EntityCollection();
        foreach ($results as $data)
        {
            $collection->add($this->buildEntity($metadata, $data));
        }

        return $collection;
    }

    public function findOneBy($class, array $conditions)
    {
        $results = $this->findBy($class, $conditions);

        return $results->count() == 0 ? null : $results->first();
    }

    public function findAll($class)
    {
        $metadata = $this->getMetadata($class);

        $results = $this->queryBuilder->table($metadata->table())->select();

        $collection = new EntityCollection();
        foreach ($results as $data)
        {
            $collection->add($this->buildEntity($metadata, $data));
        }

        return $collection;
    }

    public function persist($object)
    {
        $class = get_class($object);
        $metadata = $this->getMetadata($class);
        $r = $metadata->getReflectionClass();

        $primaryKeyProperty = $r->getProperty($metadata->primaryKey()->fieldName());
        $primaryKeyProperty->setAccessible(true);
        $value = $primaryKeyProperty->getValue($object);

        if (is_null($value))
        {
            return $this->insert($object, $metadata, $r);
        }

        return $this->update($object, $metadata, $r);
    }

    protected function insert($object, Metadata $metadata, ReflectionClass $r)
    {
        $data = [];
        $primaryKeyColumn = null;

        foreach ($metadata->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                $primaryKeyColumn = $r->getProperty($column->fieldName());
                continue;
            }

            if ($column->isForeignKey())
            {
                $data[$column->name()] = $this->handleAssociatedProperty($object, $metadata, $column, $r);
                continue;
            }

            try
            {
                $property = $r->getProperty($column->fieldName());
                $property->setAccessible(true);
            }
            catch (ReflectionException $e)
            {
                continue;
            }

            $value = $property->getValue($object);
            if (is_null($value))
            {
                if ($column->isDefault())
                {
                    continue;
                }

                if ($column->name() == Column::CREATED_AT || $column->name() == Column::UPDATED_AT)
                {
                    $data[$column->name()] = Carbon::now();
                    continue;
                }
            }

            $data[$column->name()] = $value;
        }

        $id = $this->queryBuilder->table($metadata->table())->insert($data);

        $primaryKeyColumn->setAccessible(true);
        $primaryKeyColumn->setValue($object, $id);

        $this->handleInsertAssociations($object, $metadata, $id);

        return $id;
    }

    protected function update($object, Metadata $metadata, ReflectionClass $r)
    {
        $id = 0;
        foreach ($metadata->columns() as $column)
        {
            if ($column->isForeignKey())
            {
                $data[$column->name()] = $this->handleAssociatedProperty($object, $metadata, $column, $r);
                continue;
            }

            try
            {
                $property = $r->getProperty($column->fieldName());
                $property->setAccessible(true);
            }
            catch (ReflectionException $e)
            {
                continue;
            }

            if ($column->name() == Column::UPDATED_AT)
            {
                $data[$column->name()] = Carbon::now();
                continue;
            }

            if ($column->isPrimaryKey())
            {
                $id = $property->getValue($object);
                continue;
            }

            $value = $property->getValue($object);

            $data[$column->name()] = $value;
        }

        $this->queryBuilder->table($metadata->table())
            ->where($metadata->primaryKey()->name(), '=', $id)
            ->update($data);

        $this->handleUpdateAssociations($object, $metadata, $id);

        return $id;
    }

    protected function handleAssociatedProperty($object, Metadata $metadata, Column $column, ReflectionClass $r)
    {
        $property = $r->getProperty($column->fieldName());
        $property->setAccessible(true);
        $assoc = $metadata->getAssociation($column->fieldName());

        $value = $property->getValue($object);
        if (!is_object($value))
        {
            return null;
        }

        $targetMetadata = $this->getMetadata($assoc['target']);
        $targetR = $targetMetadata->getReflectionClass();
        $targetPrimaryKey = $targetR->getProperty($targetMetadata->primaryKey()->fieldName());
        $targetPrimaryKey->setAccessible(true);

        $targetPrimaryKeyValue = $targetPrimaryKey->getValue($value);

        // case when associated entity is not persisted
        if (is_null($targetPrimaryKeyValue))
        {
            return $this->insert($value, $targetMetadata, $targetR);
        }

        return $targetPrimaryKey->getValue($value);
    }

    protected function handleInsertAssociations($object, Metadata $metadata, $objectId)
    {
        $associations = $metadata->associations();
        foreach ($associations as $fieldName => $association)
        {
            switch ($association['type'])
            {
                case Metadata::ASSOC_HAS_MANY:
                    $this->handleInsertHasMany($object, $metadata, $fieldName, $association['target'], $objectId);
                    break;
            }
        }
    }

    protected function handleInsertHasMany($object, Metadata $metadata, $fieldName, $target, $objectId)
    {
        $r = $metadata->getReflectionClass();
        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);
        $collection = $property->getValue($object);
        if (($collection instanceof EntityCollection) && $collection->isEmpty())
        {
            $property->setValue($object, new PersistentCollection($this, $r->getName()));
            return;
        }
        else if (!($collection instanceof EntityCollection))
        {
            return;
        }

        $addedItems = $collection->toArray();

        $targetMetadata = $this->getMetadata($target);
        $targetR = $targetMetadata->getReflectionClass();
        $targetProperty = $targetR->getProperty($metadata->associations()[$fieldName]['mappedBy']);
        $targetProperty->setAccessible(true);
        foreach ($addedItems as $item)
        {
            $targetAssocValue = $targetProperty->getValue($item);
            $currentIdProperty = $r->getProperty($metadata->primaryKey()->fieldName());
            $currentIdProperty->setAccessible(true);
            if (is_null($targetAssocValue) || $currentIdProperty->getValue($targetAssocValue) != $objectId)
            {
                $targetProperty->setValue($item, $object);
            }

            $targetPrimaryKeyProperty = $targetR->getProperty($targetMetadata->primaryKey()->fieldName());
            $targetPrimaryKeyProperty->setAccessible(true);
            is_null($targetPrimaryKeyProperty->getValue($item))
                ? $this->insert($item, $targetMetadata, $targetR)
                : $this->update($item, $targetMetadata, $targetR);
        }

        $property->setValue($object, new PersistentCollection($this, $r->getName(), $addedItems));
    }

    protected function handleUpdateAssociations($object, Metadata $metadata, $objectId)
    {
        $associations = $metadata->associations();
        foreach ($associations as $fieldName => $association)
        {
            switch ($association['type'])
            {
                case Metadata::ASSOC_HAS_MANY:
                    $this->handleUpdateHasMany($object, $metadata, $fieldName, $association['target'], $objectId);
                    break;
            }
        }
    }

    protected function handleUpdateHasMany($object, Metadata $metadata, $fieldName, $target, $objectId)
    {
        $r = $metadata->getReflectionClass();
        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);
        $collection = $property->getValue($object);
        if (!($collection instanceof PersistentCollection) || !$collection->isChanged())
        {
            return;
        }

        $addedItems = $collection->addedItems();
        $removedItems = $collection->removedItems();
        $targetMetadata = $this->getMetadata($target);
        $targetR = $targetMetadata->getReflectionClass();
        $targetPrimaryKeyProperty = $targetR->getProperty($targetMetadata->primaryKey()->fieldName());
        $targetPrimaryKeyProperty->setAccessible(true);
        $targetAssocProperty = $targetR->getProperty($metadata->associations()[$fieldName]['mappedBy']);
        $targetAssocProperty->setAccessible(true);

        if (sizeof($addedItems) > 0)
        {
            $ids = [];
            foreach ($addedItems as $item)
            {
                $id = $targetPrimaryKeyProperty->getValue($item);
                // if the entity was not was persisted
                if (is_null($id))
                {
                    $targetAssocProperty->setValue($item, $object);
                    $this->insert($item, $targetMetadata, $targetR);

                    continue;
                }

                $ids[] = $id;
            }

            if (sizeof($ids) > 0)
            {
                $this->queryBuilder->table($targetMetadata->table())
                    ->where($targetMetadata->primaryKey()->name(), 'in', '('.implode(',', $ids).')')
                    ->update([$metadata->generateForeignKeyName() => $objectId]);
            }
        }

        if (sizeof($removedItems) > 0)
        {
            $ids = [];
            foreach ($removedItems as $item)
            {
                $id = $targetPrimaryKeyProperty->getValue($item);
                // if the entity was not was persisted
                if (is_null($id))
                {
                    $targetAssocProperty->setValue($item, 0);
                    $this->insert($item, $targetMetadata, $targetR);

                    continue;
                }
                $ids[] = $id;
            }

            if (sizeof($ids) > 0)
            {
                $this->queryBuilder->table($targetMetadata->table())
                    ->where($targetMetadata->primaryKey()->name(), 'in', '('.implode(',', $ids).')')
                    ->update([$metadata->generateForeignKeyName() => 0]);
            }
        }

        $collection->resetState();
    }

    public function delete($classOrObject, $id = null)
    {
        $class = is_object($classOrObject)
            ? get_class($classOrObject)
            : $classOrObject;

        $metadata = $this->getMetadata($class);
        $primaryKeyFieldName = $metadata->primaryKey()->fieldName();

        if (is_null($id))
        {
            $r = $metadata->getReflectionClass();
            $primaryKey = $r->getProperty($primaryKeyFieldName);
            $primaryKey->setAccessible(true);
            $id = $primaryKey->getValue($classOrObject);
        }

        $this->queryBuilder()->table($metadata->table())
            ->where($primaryKeyFieldName, '=', $id)
            ->delete();
    }

    public function detach($object)
    {
        $class = get_class($object);

        if (!isset($this->loadedEntities[$class]))
        {
            return;
        }

        $this->loadedEntities[$class]->detach($object);
    }

    public function detachAll()
    {
        unset($this->loadedEntities);
        $this->loadedEntities = [];
    }

    public function queryBuilder()
    {
        return $this->queryBuilder;
    }

    protected function storeCommand($class, $command, array $data)
    {
        $this->storedCommands[$class][$command][] = $data;
    }

    public function getMetadata($class)
    {
        if (isset($this->loadedMetadata[$class]))
        {
            return $this->loadedMetadata[$class];
        }

        return $this->loadedMetadata[$class] = $this->mappingDriver->getMetadata($class);
    }

    protected function addToLoadedEntities($entity, $class, $id, array $foreignKeys)
    {
        if (!isset($this->loadedEntities[$class]))
        {
            $this->loadedEntities[$class] = new SplObjectStorage();
        }

        $this->loadedEntities[$class]->attach($entity, [
            'id' => $id,
            'foreignKeys' => $foreignKeys
        ]);
    }

    protected function getForeignKeys($entity, $class)
    {
        return $this->loadedEntities[$class][$entity]['foreignKeys'];
    }

    protected function findLoadedEntity($class, $id)
    {
        if (!isset($this->loadedEntities[$class]))
        {
            return null;
        }

        foreach ($this->loadedEntities[$class] as $key)
        {
            if ($this->loadedEntities[$class][$key]['id'] == $id)
            {
                return $key;
            }
        }

        return null;
    }

    protected function buildEntity(Metadata $metadata, $data)
    {
        if (sizeof($data) == 0)
        {
            return null;
        }

        $r = $metadata->getReflectionClass();

        $entity = $r->newInstanceWithoutConstructor();

        $foreignKeys = [];
        $id = 0;
        foreach ($metadata->columns() as $column)
        {
            if ($column->isForeignKey())
            {
                $foreignKeys[$column->name()] = $data[$column->name()];
                continue;
            }

            $property = $r->getProperty($column->fieldName());
            $property->setAccessible(true);

            if ($column->isPrimaryKey())
            {
                $id = $data[$column->name()];
            }

            if (!array_key_exists($column->name(), $data))
            {
                throw new RuntimeException('Could not build entity '.$r->getName()
                    .'. Column '.$column->name().' was not found in the database.');
            }

            $property->setValue($entity, $data[$column->name()]);
        }

        $this->buildAssociations($metadata, $r, $entity, $foreignKeys);

        $this->addToLoadedEntities($entity, $r->getName(), $id, $foreignKeys);

        return $entity;
    }

    protected function buildAssociations(Metadata $metadata, ReflectionClass $r, $entity, $foreignKeys)
    {
        foreach ($metadata->associations() as $fieldName => $assoc)
        {
            switch ($assoc['type'])
            {
                case Metadata::ASSOC_HAS_MANY:
                    $this->buildHasMany($assoc['target'], $fieldName, $metadata, $r, $entity);
                    break;
                case Metadata::ASSOC_HAS_ONE:
                    $this->buildHasOne($assoc['target'], $fieldName, $r, $entity, $foreignKeys);
                    break;
                case Metadata::ASSOC_BELONGS_TO:
                    $this->buildBelongsTo($assoc['target'], $fieldName, $r, $entity, $foreignKeys);
                    break;
            }
        }
    }

    protected function buildBelongsTo($target, $fieldName, ReflectionClass $r, $entity, $foreignKeys)
    {
        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);

        $targetMetadata = $this->getMetadata($target);
        $targetData = $this->queryBuilder->table($targetMetadata->table())
            ->where($targetMetadata->primaryKey()->name(), '=', $foreignKeys[$targetMetadata->generateForeignKeyName()])
            ->select();

        if (sizeof($targetData) == 0)
        {
            $property->setValue($entity, null);
            return;
        }

        $property->setValue($entity, $this->buildEntity($targetMetadata, $targetData[0]));
    }

    protected function buildHasOne($target, $fieldName, ReflectionClass $r, $entity, $foreignKeys)
    {
        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);

        $targetMetadata = $this->getMetadata($target);
        $targetData = $this->queryBuilder->table($targetMetadata->table())
            ->where($targetMetadata->primaryKey()->name(), '=', $foreignKeys[$targetMetadata->generateForeignKeyName()])
            ->select();

        if (sizeof($targetData) == 0)
        {
            $property->setValue($entity, null);
            return;
        }

        $property->setValue($entity, $this->buildEntity($targetMetadata, $targetData[0]));
    }

    protected function buildHasMany($target, $fieldName, Metadata $metadata, ReflectionClass $r, $entity)
    {
        $targetMetadata = $this->getMetadata($target);
        $thisPrimaryKey = $r->getProperty($metadata->primaryKey()->fieldName());
        $thisPrimaryKey->setAccessible(true);
        $thisPrimaryKeyValue = $thisPrimaryKey->getValue($entity);

        $results = $this->queryBuilder->table($targetMetadata->table())
            ->where($metadata->generateForeignKeyName(), '=', $thisPrimaryKeyValue)
            ->select([$targetMetadata->primaryKey()->name()]);
        $targetIds = [];
        foreach ($results as $targetId)
        {
            $targetIds[] = $targetId;
        }

        $collection = new PersistentCollection($this, $targetMetadata->getReflectionClass()->getName(), $targetIds);

        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);
        $property->setValue($entity, $collection);
    }
}
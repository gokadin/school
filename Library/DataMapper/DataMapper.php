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

    public function find($class, $id)
    {
        $metadata = $this->loadMetadata($class);

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

    public function findBy($class, array $conditions)
    {
        $metadata = $this->loadMetadata($class);

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
        $metadata = $this->loadMetadata($class);

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
        $metadata = $this->loadMetadata($class);
        $r = $metadata->getReflectionClass();

        $primaryKeyProperty = $r->getProperty($metadata->primaryKey()->fieldName());
        $primaryKeyProperty->setAccessible(true);
        $value = $primaryKeyProperty->getValue($object);

        if (is_null($value))
        {
            $this->insert($object, $metadata, $r);
            return;
        }

        $this->update($object, $metadata, $r);
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

        $targetMetadata = $this->loadMetadata($assoc['target']);
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
        if (!($collection instanceof EntityCollection) || $collection->isEmpty())
        {
            return;
        }

        $addedItems = $collection->toArray();

        $targetMetadata = $this->loadMetadata($target);
        $targetR = $targetMetadata->getReflectionClass();
        $targetProperty = $targetR->getProperty($metadata->generateForeignKeyName());
        $targetProperty->setAccessible(true);
        foreach ($addedItems as $item)
        {
            if ($targetProperty->getValue($item) == $objectId)
            {
                continue;
            }

            $targetProperty->setValue($item, $objectId);
            $this->update($item, $targetMetadata, $targetR);
        }
    }

    public function delete($classOrObject, $id = null)
    {
        $class = is_object($classOrObject)
            ? get_class($classOrObject)
            : $classOrObject;

        $metadata = $this->loadMetadata($class);
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

//    public function flush()
//    {
//        foreach ($this->storedCommands as $class => $commands)
//        {
//            foreach ($commands as $command => $data)
//            {
//                switch ($command)
//                {
//                    case 'insert':
//
//                        break;
//                    case 'update':
//
//                        break;
//                }
//            }
//        }
//    }

    public function queryBuilder()
    {
        return $this->queryBuilder;
    }

    protected function storeCommand($class, $command, array $data)
    {
        $this->storedCommands[$class][$command][] = $data;
    }

    protected function loadMetadata($class)
    {
        if (isset($this->loadedMetadata[$class]))
        {
            return $this->loadedMetadata[$class];
        }

        return $this->loadedMetadata[$class] = $this->mappingDriver->getMetadata($class);
    }

    protected function addToLoadedEntities($entity, $class, array $foreignKeys)
    {
        if (!isset($this->loadedEntities[$class]))
        {
            $this->loadedEntities[$class] = new SplObjectStorage();
        }

        $this->loadedEntities[$class][$entity] = [
            'foreignKeys' => $foreignKeys
        ];
    }

    protected function getForeignKeys($entity, $class)
    {
        return $this->loadedEntities[$class][$entity]['foreignKeys'];
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
        foreach ($metadata->columns() as $column)
        {
            if ($column->isForeignKey())
            {
                $foreignKeys[$column->name()] = $data[$column->name()];
                continue;
            }

            $property = $r->getProperty($column->fieldName());
            $property->setAccessible(true);

            if (!array_key_exists($column->name(), $data))
            {
                throw new RuntimeException('Could not build entity '.$r->getName()
                    .'. Column '.$column->name().' was not found in the database.');
            }

            $property->setValue($entity, $data[$column->name()]);
        }

        $this->buildAssociations($metadata, $r, $entity, $foreignKeys);

        $this->addToLoadedEntities($entity, $r->getName(), $foreignKeys);

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

        $targetMetadata = $this->loadMetadata($target);
        $targetData = $this->queryBuilder->table($targetMetadata->table())
            ->where($targetMetadata->primaryKey()->name(), '=', $foreignKeys[$targetMetadata->generateForeignKeyName()])
            ->select();

        if (sizeof($targetData) == 0)
        {
            $property->setValue($entity, null);
        }

        $property->setValue($entity, $this->buildEntity($targetMetadata, $targetData[0]));
    }

    protected function buildHasMany($target, $fieldName, Metadata $metadata, ReflectionClass $r, $entity)
    {
        $targetMetadata = $this->loadMetadata($target);
        $thisPrimaryKey = $r->getProperty($metadata->primaryKey()->fieldName());
        $thisPrimaryKey->setAccessible(true);
        $thisPrimaryKeyValue = $thisPrimaryKey->getValue($entity);

        $targetIds = $this->queryBuilder->table($targetMetadata->table())
            ->where($metadata->generateForeignKeyName(), '=', $thisPrimaryKeyValue)
            ->select([$targetMetadata->primaryKey()->name()]);

        $collection = new PersistentCollection($this, $r->getName(), $targetIds);

        $property = $r->getProperty($fieldName);
        $property->setAccessible(true);
        $property->setValue($entity, $collection);
    }
}
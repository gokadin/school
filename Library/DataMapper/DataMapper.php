<?php

namespace Library\DataMapper;

use Carbon\Carbon;
use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\Mapping\Column;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use Library\DataMapper\Mapping\Metadata;
use Symfony\Component\Yaml\Exception\RuntimeException;
use ReflectionClass;
use ReflectionException;

class DataMapper
{
    protected $mappingDriver;
    protected $queryBuilder;
    protected $loadedMetadata = [];

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

        $entities = [];
        foreach ($results as $data)
        {
            $entities[] = $this->buildEntity($metadata, $data);
        }

        return $entities;
    }

    public function findOneBy($class, array $conditions)
    {
        $results = $this->findBy($class, $conditions);

        return sizeof($results) == 0 ? null : $results[0];
    }

    public function findAll($class)
    {
        $metadata = $this->loadMetadata($class);

        $results = $this->queryBuilder->table($metadata->table())->select();

        $entities = [];
        foreach ($results as $data)
        {
            $entities[] = $this->buildEntity($metadata, $data);
        }

        return $entities;
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
    }

    protected function update($object, Metadata $metadata, ReflectionClass $r)
    {
        $id = 0;
        foreach ($metadata->columns() as $column)
        {
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

    protected function buildEntity(Metadata $metadata, $data)
    {
        $r = $metadata->getReflectionClass();

        $entity = $r->newInstanceWithoutConstructor();

        foreach ($metadata->columns() as $column)
        {
            $property = $r->getProperty($column->fieldName());
            $property->setAccessible(true);

            if (!array_key_exists($column->name(), $data))
            {
                throw new RuntimeException('Could not build entity '.$r->getName()
                    .'. Column '.$column->name().' was not found in the database.');
            }

            $property->setValue($entity, $data[$column->name()]);
        }

        return $entity;
    }
}
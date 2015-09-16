<?php

namespace Library\DataMapper;

use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use Library\DataMapper\Mapping\Metadata;
use Symfony\Component\Yaml\Exception\RuntimeException;

class DataMapper
{
    protected $mappingDriver;
    protected $queryBuilder;
    protected $loadedMetadata = [];
    protected $storedCommands = [];

    public function __construct($config)
    {
        $this->initializeMetadataDriver($config['mappingDriver']);

        $this->queryBuilder = new QueryBuilder($config);
    }

    protected function initializeMetadataDriver($driverName)
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

        $data = $this->getQueryBuilder()->select()
            ->from($metadata->table())
            ->where($metadata->primaryKey()->columnName(), '=', $id)
            ->execute();

        if (sizeof($data) == 0)
        {
            return null;
        }

        return $this->buildEntity($metadata, $data);
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

    public function persist($object)
    {
        $this->storeCommand(get_class($object), 'persist', [$object]);
    }

    public function delete($classOrObject, $id = 0)
    {
        $class = is_object($classOrObject)
            ? get_class($classOrObject)
            : $classOrObject;

        $this->storeCommand($class, 'delete', [$id]);
    }

    public function flush()
    {
        foreach ($this->storedCommands as $class => $commands)
        {
            foreach // ... stopped here
        }
    }

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
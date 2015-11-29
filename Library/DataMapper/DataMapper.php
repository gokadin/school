<?php

namespace Library\DataMapper;

use Library\DataMapper\Collection\EntityCollection;
use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use Library\DataMapper\Mapping\Metadata;
use Library\DataMapper\UnitOfWork\UnitOfWork;

class DataMapper
{
    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * The mapping driver used to parse
     * and build entity metadata.
     *
     * @var MappingDriverInterface
     */
    protected $mappingDriver;

    /**
     * Used to build a queries differently
     * depending on which database driver is configured.
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Already parsed and loaded metadata from
     * the mapping driver.
     *
     * @var array
     */
    protected $loadedMetadata = [];

    /**
     * Initializes mapping and database drivers.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->unitOfWork = new UnitOfWork($this);

        $this->initializeMappingDriver($config['mappingDriver']);

        $this->queryBuilder = new QueryBuilder($config);
    }

    /**
     * Initializes the mapping driver.
     *
     * @param $driverName
     */
    protected function initializeMappingDriver($driverName)
    {
        switch ($driverName)
        {
            default:
                $this->mappingDriver = new AnnotationDriver();
                break;
        }
    }

    /**
     * Finds the object by id and fully loads it
     * in the unit of work unless already there.
     *
     * @param $class
     * @param $id
     * @return mixed
     */
    public function find($class, $id)
    {
        $entity = $this->unitOfWork->find($class, $id);

        if (!is_null($entity))
        {
            return $entity;
        }

        $metadata = $this->getMetadata($class);

        $data = $this->queryBuilder()->table($metadata->table())
            ->where($metadata->primaryKey()->propName(), '=', $id)
            ->select();

        if (is_null($data) || sizeof($data) == 0)
        {
            return null;
        }

        $entity = $this->buildEntity($class, $data[0]);
        $this->unitOfWork->addManaged($entity, $data[0]);

        return $entity;
    }

    /**
     * Finds an object by id and fully loads it.
     * Throws an exception if not found.
     *
     * @param $class
     * @param $id
     * @return mixed
     * @throws DataMapperException
     */
    public function findOrFail($class, $id)
    {
        $entity = $this->find($class, $id);

        if (is_null($entity))
        {
            throw new DataMapperException('DataMapper.findOrFail : Could not find entity of class '.$class.
                ' with id of '.$id);
        }

        return $entity;
    }

    /**
     * Finds all the objects of the given class.
     *
     * @param $class
     * @return EntityCollection
     */
    public function findAll($class)
    {
        $metadata = $this->getMetadata($class);
        $allData = $this->queryBuilder->table($metadata->table())
            ->select();

        if (sizeof($allData) == 0)
        {
            return new EntityCollection();
        }

        $collection = new EntityCollection();
        foreach ($allData as $data)
        {
            $entity = $this->buildEntity($class, $data);
            $this->unitOfWork->addManaged($entity, $data);
            $collection->add($entity);
        }

        return $collection;
    }

    /**
     * Marks the object for persistence
     * and adds it to the unit of work if not yet tracked.
     *
     * @param $object
     */
    public function persist($object)
    {
        $id = $this->getId($object);

        if (is_null($id))
        {
            $this->unitOfWork->addNew($object);
        }
    }

    public function delete($object)
    {
        $this->unitOfWork->addToRemovals($object);
    }

    /**
     * Executes all scheduled work in the unit of work.
     */
    public function flush()
    {
        $this->unitOfWork->commit();
    }

    /**
     * Stops tracking the object in the unit of work.
     *
     * @param $object
     */
    public function detach($object)
    {
        $this->unitOfWork->detach($object);
    }

    /**
     * Stops tracking all objects in the unit of work.
     */
    public function detachAll()
    {
        $this->unitOfWork->detachAll();
    }

    /**
     * Get the query builder.
     *
     * @return QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Gets the metadata for the class
     * and loads it if not yet loaded.
     *
     * @param $class
     * @return Metadata
     */
    public function getMetadata($class)
    {
        if (isset($this->loadedMetadata[$class]))
        {
            return $this->loadedMetadata[$class];
        }

        return $this->loadedMetadata[$class] = $this->mappingDriver->getMetadata($class);
    }

    /**
     * Gets the object id with reflection.
     *
     * @param $object
     * @return mixed
     */
    protected function getId($object)
    {
        $metadata = $this->getMetadata(get_class($object));
        $r = $metadata->getReflectionClass();
        $property = $r->getProperty($metadata->primaryKey()->name());
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    protected function buildEntity($class, $data)
    {
        $metadata = $this->getMetadata($class);
        $r = $metadata->getReflectionClass();

        $entity = $r->newInstanceWithoutConstructor();

        foreach ($metadata->columns() as $column)
        {
            $property = $r->getProperty($column->propName());
            $property->setAccessible(true);
            $property->setValue($entity, $data[$column->name()]);
        }

        return $entity;
    }
}
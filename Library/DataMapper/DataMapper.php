<?php

namespace Library\DataMapper;

use Library\DataMapper\Database\QueryBuilder;
use Library\DataMapper\Mapping\Drivers\AnnotationDriver;
use Library\DataMapper\Mapping\Metadata;

class DataMapper
{
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
     * Main array of the unit of work pattern.
     *
     * @var array
     */
    protected $loadedEntities = [];

    /**
     * Initializes mapping and database drivers.
     *
     * @param array $config
     */
    public function __construct($config)
    {
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

    }

    /**
     * Marks the object for persistence
     * and adds it to the unit of work if not yet tracked.
     *
     * @param $object
     */
    public function persist($object)
    {

    }

    /**
     * Stops tracking the object in the unit of work.
     *
     * @param $object
     */
    public function detach($object)
    {
        $class = get_class($object);

        if (!isset($this->loadedEntities[$class]))
        {
            return;
        }

        $this->loadedEntities[$class]->detach($object);
    }

    /**
     * Stops tracking all objects in the unit of work.
     */
    public function detachAll()
    {
        unset($this->loadedEntities);

        $this->loadedEntities = [];
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
}
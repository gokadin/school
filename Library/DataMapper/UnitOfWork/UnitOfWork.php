<?php

namespace Library\DataMapper\UnitOfWork;

use Library\DataMapper\DataMapper;
use Exception;

/**
 * Keeps track of all entities known to data mapper
 * and their states.
 */
class UnitOfWork
{
    /**
     * @var DataMapper
     */
    protected $dm;

    /**
     * Map of all fully loaded entities sorted by class.
     * Uses ids as keys.
     *
     * @var array
     */
    protected $managed = [];

    protected $known = [];

    /**
     * The original property values of entities.
     * Uses spl_object_hash as keys.
     *
     * @var array
     */
    protected $originalData = [];

    /**
     * @var array
     */
    protected $scheduledInsertions = [];

    /**
     * @var array
     */
    protected $scheduledUpdates = [];

    /**
     * @var array
     */
    protected $scheduledRemovals = [];

    /**
     * @param DataMapper $dm
     */
    public function __construct(DataMapper $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Adds the fully loaded entity to the unit of work.
     *
     * @param $entity
     * @param array $data
     */
    public function startTracking($entity, array $data)
    {
        $metadata = $this->dm->getMetadata(get_class($entity));
        $id = $data[$metadata->primaryKey()->fieldName()];

        $this->addToManaged($entity, $id);

        $this->addOriginalData($entity, $data);
    }

    /**
     * @param $entity
     * @param $id
     */
    protected function addToManaged($entity, $id)
    {
        $this->managed[get_class($entity)][$id] = $entity;
    }

    /**
     * @param $entity
     * @param array $data
     */
    protected function addOriginalData($entity, array $data)
    {
        $this->originalData[spl_object_hash($entity)] = $data;
    }

    /**
     * @param $class
     * @param $id
     * @return null
     */
    public function find($class, $id)
    {
        if (isset($this->managed[$class][$id]))
        {
            return $this->managed[$class][$id];
        }

        return null;
    }

    /**
     * Schedules the entity for insertion
     *
     * @param $entity
     */
    public function scheduleInsertion($entity)
    {
        $this->insertions[get_class($entity)][] = $entity;
    }

    /**
     * Executes all scheduled work in the unit of work.
     */
    public function flush()
    {
        $this->dm->queryBuilder()->beginTransaction();

        try
        {
            $this->executeInsertions();

            $this->dm->queryBuilder()->commit();
        }
        catch (Exception $e)
        {
            $this->dm->queryBuilder()->rollBack();
            // LOG
        }
    }

    /**
     * Executes all insertions
     */
    protected function executeInsertions()
    {
        foreach ($this->scheduledInsertions as $class => $entities)
        {
            $metadata = $this->dm->getMetadata($class);
            $r = $metadata->getReflectionClass();
            $queryBuilder = $this->dm->queryBuilder()->table($metadata->table());

            $dataSet = [];
            foreach ($entities as $entity)
            {
                $data = [];
                foreach ($metadata->columns() as $column)
                {
                    $property = $r->getProperty($column->name())->setAccessible(true);
                    $data[$column->fieldName()] = $property->getValue($entity);
                }

                $dataSet[] = $data;
            }

            $queryBuilder->insertMany($dataSet);

            $this->startTracking() // MANAGE MY OWN IdS?...
        }
    }
}
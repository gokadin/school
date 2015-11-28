<?php

namespace Library\DataMapper\UnitOfWork;

use Carbon\Carbon;
use Library\DataMapper\DataMapper;
use Exception;
use Library\DataMapper\Mapping\Column;

/**
 * Keeps track of all entities known to data mapper
 * and their states.
 */
final class UnitOfWork
{
    /**
     * Entity does not exist in the database.
     */
    const STATE_NEW = 'STATE_NEW';

    /**
     * Entity exists in the database, but data mapper only
     * has its id.
     */
    const STATE_KNOWN = 'STATE_KNOWN';

    /**
     * Entity exists in the database and is fully loaded in the
     * unit of work.
     */
    const STATE_MANAGED = 'STATE_MANAGED';

    /**
     * @var DataMapper
     */
    private $dm;

    /**
     * Links every entity's hash with its object.
     *
     * @var array
     */
    private $entities = [];

    /**
     * Represents every known id associated with its object hash, sorted by class.
     * If the entity state is known, the id will be associated to null.
     *
     * @var array
     */
    private $idMap = [];

    /**
     * Links every entity's hash to its id.
     *
     * @var array
     */
    private $ids = [];

    /**
     * Links every entity's hash to its original data.
     *
     * @var array
     */
    private $originalData = [];

    /**
     * Links every entity's hash with its current state.
     *
     * @var array
     */
    private $states = [];

    /**
     * Sorted by class, then by hash.
     *
     * @var array
     */
    private $scheduledInsertions = [];

    /**
     * Sorted by class, then by hash.
     *
     * @var array
     */
    private $scheduledUpdates = [];

    /**
     * Sorted by class, then by hash.
     *
     * @var array
     */
    private $scheduledRemovals = [];

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
    public function addManaged($entity, array $data)
    {
        $class = get_class($entity);
        $metadata = $this->dm->getMetadata($class);
        $oid = spl_object_hash($entity);
        $id = $data[$metadata->primaryKey()->name()];

        $this->entities[$oid] = $entity;

        $this->idMap[$class][$id] = $oid;

        $this->ids[$oid] = $id;

        $this->originalData[$oid] = $data;

        $this->states[$oid] = self::STATE_MANAGED;
    }

    /**
     * Adds a new entity to the unit of work
     * which does not yet exist in the database.
     *
     * @param $entity
     */
    public function addNew($entity)
    {
        $oid = spl_object_hash($entity);

        $this->entities[$oid] = $entity;

        $this->states[$oid] = self::STATE_NEW;

        $this->scheduleInsertion(get_class($entity), $oid);
    }

    /**
     * @param $class
     * @param $id
     * @return null
     */
    public function find($class, $id)
    {
        if (isset($this->idMap[$class][$id]))
        {
            return $this->entities[$this->idMap[$class][$id]];
        }

        return null;
    }

    public function detach($object)
    {
        $oid = spl_object_hash($object);

        // ...
    }

    public function detachAll()
    {
        $this->entities = [];
        $this->idMap = [];
        $this->ids = [];
        $this->originalData = [];
        $this->states = [];
        $this->clearInsertions();
    }

    /**
     * Schedules the entity for insertion
     *
     * @param $class
     * @param $oid
     */
    public function scheduleInsertion($class, $oid)
    {
        $this->scheduledInsertions[$class][] = $oid;
    }

    /**
     * Clears all scheduled insertions.
     */
    public function clearInsertions()
    {
        $this->insertions = [];
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
    private function executeInsertions()
    {
        foreach ($this->scheduledInsertions as $class => $oids)
        {
            $metadata = $this->dm->getMetadata($class);
            $r = $metadata->getReflectionClass();
            $queryBuilder = $this->dm->queryBuilder()->table($metadata->table());

            $dataSet = [];
            foreach ($oids as $oid)
            {
                $entity = $this->entities[$oid];
                $data = [];

                foreach ($metadata->columns() as $column)
                {
                    if ($column->isPrimaryKey())
                    {
                        continue;
                    }

                    $property = $r->getProperty($column->propName());
                    $property->setAccessible(true);
                    $value = $property->getValue($entity);

                    if ($column->name() == Column::CREATED_AT && is_null($value) ||
                        $column->name() == Column::UPDATED_AT && is_null($value))
                    {
                        $value = Carbon::now();
                    }

                    if (!is_null($value))
                    {
                        $data[$column->name()] = $value;
                    }
                }

                $dataSet[] = $data;
            }

            $ids = $queryBuilder->insertMany($dataSet);

            for ($i = 0; $i < sizeof($oids); $i++)
            {
                $property = $r->getProperty($metadata->primaryKey()->name());
                $property->setAccessible(true);
                $property->setValue($this->entities[$oids[$i]], $ids[$i]);

                $dataSet[$i][$metadata->primaryKey()->name()] = $ids[$i];
                $this->addManaged($this->entities[$oids[$i]], $dataSet[$i]);
            }
        }

        $this->clearInsertions();
    }
}
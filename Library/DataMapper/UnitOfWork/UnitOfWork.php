<?php

namespace Library\DataMapper\UnitOfWork;

use Carbon\Carbon;
use Library\DataMapper\DataMapper;
use Exception;
use Library\DataMapper\DataMapperException;
use Library\DataMapper\Mapping\Metadata;
use Library\DataMapper\Persisters\BatchEntityPersister;
use Library\DataMapper\Persisters\EntityPersister;
use Library\DataMapper\Persisters\SingleEntityPersister;

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
     * @var array
     */
    private $visitedEntities = [];

    /**
     * Sorted by class, then by hash.
     *
     * @var array
     */
    private $scheduledInsertions = [];

    /**
     * Links all the changes found with each class as key. var
     *
     * @var array
     */
    private $scheduledUpdates = [];

    /**
     * Sorted by class.
     *
     * @var array
     */
    private $scheduledExtraUpdates = [];

    /**
     * Sorted by class, then by hash.
     *
     * @var array
     */
    private $scheduledRemovals = [];

    /**
     * Entity persisters with classes as keys.
     *
     * @var array
     */
    private $entityPersisters = [];

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
        $oid = spl_object_hash($entity);

        if (isset($this->states[$oid]))
        {
            switch ($this->states[$oid])
            {
                case self::STATE_MANAGED:
                    return;
                case self::STATE_KNOWN:
                    // ...
                    break;
            }
        }

        $class = get_class($entity);
        $metadata = $this->dm->getMetadata($class);
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

        if (isset($this->states[$oid]) && $this->states[$oid] == self::STATE_NEW)
        {
            return;
        }

        $this->entities[$oid] = $entity;

        $this->states[$oid] = self::STATE_NEW;

        $this->scheduleInsertion(get_class($entity), $oid);
    }

    /**
     * Marks the entity for deletion.
     * The entity must be managed by the unit of work.
     *
     * @param $entity
     * @throws DataMapperException
     */
    public function addToRemovals($entity)
    {
        $oid = spl_object_hash($entity);

        if (!isset($this->states[$oid]))
        {
            throw new DataMapperException('UnitOfWork.addToRemovals : Entity of class '.get_class($entity).
                ' cannot be removed as it is not known by the unit of work.');
        }

        $this->scheduleRemoval(get_class($entity), $oid);
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

    /**
     * Detach an entity.
     *
     * @param $entity
     */
    public function detach($entity)
    {
        $oid = spl_object_hash($entity);

        if (!isset($this->states[$oid]))
        {
            return;
        }

        switch ($this->states[$oid])
        {
            case self::STATE_NEW:
                $this->detachNew($oid);
                break;
            case self::STATE_KNOWN:
                $this->detachKnown($oid);
                break;
            case self::STATE_MANAGED:
                $this->detachManaged($oid);
                break;
        }
    }

    /**
     * Detaches a managed entity.
     *
     * @param $oid
     */
    private function detachManaged($oid)
    {
        $entity = $this->entities[$oid];
        $class = get_class($entity);
        $metadata = $this->dm->getMetadata($class);
        $id = $metadata->reflProp($metadata->primaryKey()->name())->getValue($entity);

        unset($this->entities[$oid]);
        unset($this->ids[$oid]);
        unset($this->originalData[$oid]);
        unset($this->idMap[$entity][$id]);
        unset($this->states[$oid]);
    }

    /**
     * Detaches a known entity.
     *
     * @param $oid
     */
    private function detachKnown($oid)
    {
        unset($this->ids[$oid]);
        unset($this->states[$oid]);
    }

    /**
     * Detaches a new entity.
     *
     * @param $oid
     */
    private function detachNew($oid)
    {
        unset($this->entities[$oid]);
        unset($this->states[$oid]);
    }

    /**
     * Detaches all entities from the unit of work.
     */
    public function detachAll()
    {
        $this->entities = [];
        $this->idMap = [];
        $this->ids = [];
        $this->originalData = [];
        $this->states = [];
        $this->clearInsertions();
        $this->clearRemovals();
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
     * Schedules the entity for deletion.
     *
     * @param $class
     * @param $oid
     */
    public function scheduleRemoval($class, $oid)
    {
        $this->scheduledRemovals[$class][] = $oid;
    }

    /**
     * Schedules the entity for extra update.
     *
     * @param $class
     * @param $oid
     */
    private function scheduleExtraUpdate($class, $oid)
    {
        $this->scheduledExtraUpdates[$class][] = $oid;
    }

    /**
     * Clears all scheduled insertions.
     */
    private function clearInsertions()
    {
        $this->scheduledInsertions = [];
    }

    /**
     * Clears all scheduled removals.
     */
    private function clearRemovals()
    {
        $this->scheduledRemovals = [];
    }

    /**
     * Clears all scheduled updates.
     */
    private function clearUpdates()
    {
        $this->scheduledUpdates = [];
    }

    /**
     * Clears all extra updates.
     */
    private function clearExtraUpdates()
    {
        $this->scheduledExtraUpdates = [];
    }

    /**
     * Gets the class entity persister.
     *
     * @param $class
     * @return EntityPersister
     */
    private function getEntityPersister($class)
    {
        if (!isset($this->entityPersisters[$class]))
        {
            $this->entityPersisters[$class] = new EntityPersister($this->dm, $this, $class);
        }

        return $this->entityPersisters[$class];
    }

    /**
     * Figures out the dependecies for all classes and
     * builds an order in which they must be commited.
     *
     * @return array
     */
    private function getCommitOrder()
    {
        return [];
    }

    /**
     * Executes all scheduled work in the unit of work.
     */
    public function commit()
    {
        $this->dm->queryBuilder()->beginTransaction();

        $commitOrder = $this->getCommitOrder();

        try
        {
            foreach ($commitOrder as $class)
            {
                $this->executeInsertions($class);
            }

            foreach ($commitOrder as $class)
            {
                $this->executeUpdates($class);
            }

            foreach ($commitOrder as $class)
            {
                $this->executeRemovals($class);
            }

            foreach ($commitOrder as $class)
            {
                $this->executeExtraUpdates($class);
            }

            $this->dm->queryBuilder()->commit();
        }
        catch (Exception $e)
        {
            $this->dm->queryBuilder()->rollBack();

            throw new DataMapperException('UnitOfWork.commit : '.$e->getMessage());
        }
    }

    /**
     * Executes all insertions
     *
     * @param $class
     */
    private function executeInsertions($class)
    {
        $persister = $this->getEntityPersister($class);
        $metadata = $this->dm->getMetadata($class);
        $oids = $this->scheduledInsertions[$class];

        foreach ($oids as $oid)
        {
            $data = $this->prepareInsertionData($metadata, $oid);

            $persister->addInsert($oid, $data);
        }

        $ids = $persister->executeInserts();

        foreach ($oids as $oid)
        {
            $metadata->reflProp($metadata->primaryKey()->name())->setValue($this->entities[$oid], $ids[$oid]);
            if ($metadata->hasCreatedAt())
            {
                $metadata->reflProp($metadata->createdAt()->propName())
                    ->setValue($this->entities[$oid], $this->originalData[$oid][$metadata->createdAt()->name()]);
            }
            if ($metadata->hasUpdatedAt())
            {
                $metadata->reflProp($metadata->updatedAt()->propName())
                    ->setValue($this->entities[$oid], $this->originalData[$oid][$metadata->updatedAt()->name()]);
            }

            $this->originalData[$oid][$metadata->primaryKey()->name()] = $ids[$oid];
            $this->addManaged($this->entities[$oid], $this->originalData[$oid]);
        }

        $this->clearInsertions();
    }

    private function prepareInsertionData(Metadata $metadata, $oid)
    {
        $data = [];

        foreach ($metadata->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                continue;
            }

            $value = $metadata->reflProp($column->propName())->getValue($this->entities[$oid]);

            if ($column->isForeignKey())
            {
                $value = $this->processAssociationForInsert($this->entities[$oid],
                    $metadata->getAssociation($column->propName()));

                if (!is_null($value))
                {
                    $data[$column->name()] = null;
                }

                $this->originalData[$oid][$column->name()] = $value;
                continue;
            }

            if ($column->isTimestamp() && is_null($value))
            {
                $value = Carbon::now();
            }

            if (!is_null($value))
            {
                $data[$column->name()] = $value;
            }

            $this->originalData[$oid][$column->name()] = $value;
        }

        return $data;
    }

    /**
     * @param $entity
     * @param $association
     * @return null|object
     */
    private function processAssociationForInsert($entity, $association)
    {
        switch ($association['type'])
        {
            case Metadata::ASSOC_HAS_ONE:
                return $this->processHasOneForInsert($entity, $association);
        }
    }

    private function processHasOneForInsert($entity, $association)
    {
        $metadata = $this->dm->getMetadata(get_class($entity));
        $column = $association['column'];

        $assocValue = $metadata->reflProp($column->propName())->getValue($entity);

        if (is_null($assocValue))
        {
            if ($association['isNullable'])
            {
                return null;
            }

            throw new DataMapperException('UnitOfWork.processHasOneForInsert : Association on '
                .$column->propName().' cannot be null.');
        }

        $assocOid = spl_object_hash($assocValue);

        if (!isset($this->states[$assocOid]))
        {
            throw new DataMapperException('UnitOfWork.processHasOneForInsert : Associated entity for '
                .$column->propName().' was not persisted.');
        }

        // association was not yet persisted
        if ($this->states[$assocOid] == self::STATE_NEW)
        {
            $this->scheduleExtraUpdate($metadata->className(), spl_object_hash($entity));

            return null;
        }

        return $this->ids[$assocOid];
    }

    /**
     * Executes all removals.
     */
    private function executeRemovals()
    {
        foreach ($this->scheduledRemovals as $class => $oids)
        {
            if (sizeof($oids) == 1)
            {
                $this->getSinglePersister($class)->executeRemoval($this->ids[$oids[0]]);
            }

            $ids = [];
            foreach ($oids as $oid)
            {
                $ids[] = $this->ids[$oid];
                $this->detachManaged($oid);
            }

            $this->getBatchPersister($class)->executeRemovals($ids);
        }

        $this->clearRemovals();
    }

    /**
     * Executes all scheduled updates
     */
    private function executeUpdates()
    {
        $this->detectEntityChanges();

        foreach ($this->scheduledUpdates as $class => $allChanges)
        {
            $updateSet = [];
            $ids = [];
            foreach ($allChanges as $id => $changeSet)
            {
                foreach ($changeSet as $field => $value)
                {
                    $updateSet[$field][$id] = $value;
                    $ids[] = $id;
                }
            }

            $metadata = $this->dm->getMetadata($class);
            $this->dm->queryBuilder()->table($metadata->table())
                ->where($metadata->primaryKey()->name(), 'in', '('.implode(',', $ids).')')
                ->updateMany($updateSet, $metadata->primaryKey()->name());
        }

        $this->clearUpdates();
    }

    /**
     * Loops through all managed entities to detect changes
     * from the original values and schedules them from update.
     */
    private function detectEntityChanges()
    {
        foreach ($this->idMap as $class => $idData)
        {
            $metadata = $this->dm->getMetadata($class);
            $allChanges = [];
            foreach ($idData as $id => $oid)
            {
                $entity = $this->entities[$oid];

                $changeSet = $this->buildChangeSet($entity, $oid, $metadata);
                if (sizeof($changeSet) == 0)
                {
                    continue;
                }

                $allChanges[$id] = $changeSet;
            }

            if (sizeof($allChanges) == 0)
            {
                continue;
            }

            $this->scheduledUpdates[$class] = $allChanges;
        }
    }

    /**
     * Compares the entity with its original data for changes.
     *
     * @param $entity
     * @param $oid
     * @param Metadata $metadata
     * @return array $changeSet
     */
    private function buildChangeSet($entity, $oid, Metadata $metadata)
    {
        $changeSet = [];
        $originalData = $this->originalData[$oid];

        foreach ($metadata->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                continue;
            }

            $actualValue = $metadata->reflProp($column->propName())->getValue($entity);

            if ($originalData[$column->name()] == $actualValue)
            {
                continue;
            }

            $changeSet[$column->name()] = $actualValue;
        }

        return $changeSet;
    }
}
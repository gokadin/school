<?php

namespace Library\DataMapper\UnitOfWork;

use Carbon\Carbon;
use Library\DataMapper\DataMapper;
use Exception;
use Library\DataMapper\DataMapperException;
use Library\DataMapper\Mapping\Column;
use Library\DataMapper\Mapping\Metadata;
use Symfony\Component\Translation\Tests\IdentityTranslatorTest;

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
     * Links all the changes found with each class as key. var
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

        unset($this->entities[$oid]);
        unset($this->ids[$oid]);
        unset($this->originalData[$oid]);
        unset($this->idMap[get_class($entity)][$this->getId($entity)]);
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
     * Executes all scheduled work in the unit of work.
     */
    public function commit()
    {
        $this->dm->queryBuilder()->beginTransaction();

        try
        {
            $this->executeRemovals();
            $this->executeUpdates();
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
            $originalDataSet = [];
            foreach ($oids as $oid)
            {
                $entity = $this->entities[$oid];
                $data = [];
                $originalData = [];
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

                    $originalData[$column->name()] = $value;
                }

                $dataSet[] = $data;
                $originalDataSet[] = $originalData;
            }

            $ids = $queryBuilder->insertMany($dataSet);

            for ($i = 0; $i < sizeof($oids); $i++)
            {
                $property = $r->getProperty($metadata->primaryKey()->name());
                $property->setAccessible(true);
                $property->setValue($this->entities[$oids[$i]], $ids[$i]);
                $createdAt = $r->getProperty($metadata->createdAt()->propName());
                $createdAt->setAccessible(true);
                $createdAt->setValue($this->entities[$oids[$i]], $dataSet[$i][$metadata->createdAt()->name()]);
                $updatedAt = $r->getProperty($metadata->updatedAt()->propName());
                $updatedAt->setAccessible(true);
                $updatedAt->setValue($this->entities[$oids[$i]], $dataSet[$i][$metadata->updatedAt()->name()]);

                $originalDataSet[$i][$metadata->primaryKey()->name()] = $ids[$i];
                $this->addManaged($this->entities[$oids[$i]], $originalDataSet[$i]);
            }
        }

        $this->clearInsertions();
    }

    private function executeRemovals()
    {
        foreach ($this->scheduledRemovals as $class => $oids)
        {
            $ids = [];
            foreach ($oids as $oid)
            {
                $ids[] = $this->ids[$oid];
                $this->detachManaged($oid);
            }

            $metadata = $this->dm->getMetadata($class);

            $this->dm->queryBuilder()->table($metadata->table())
                ->where($metadata->primaryKey()->name(), 'in', '('.implode(',', $ids).')')
                ->delete();
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
        $r = $metadata->getReflectionClass();

        foreach ($metadata->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                continue;
            }

            $prop = $r->getProperty($column->propName());
            $prop->setAccessible(true);
            $actualValue = $prop->getValue($entity);

            if ($originalData[$column->name()] == $actualValue)
            {
                continue;
            }

            $changeSet[$column->name()] = $actualValue;
        }

        return $changeSet;
    }

    /**
     * Gets the object id with reflection.
     *
     * @param $object
     * @return mixed
     */
    protected function getId($object)
    {
        $metadata = $this->dm->getMetadata(get_class($object));
        $r = $metadata->getReflectionClass();
        $property = $r->getProperty($metadata->primaryKey()->name());
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
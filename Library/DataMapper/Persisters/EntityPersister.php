<?php

namespace Library\DataMapper\Persisters;

class EntityPersister extends BasePersister
{
    protected $inserts = [];

    protected $removalIds = [];

    public function addInsert($oid, array $data)
    {
        $this->inserts[$oid] = $data;
    }

    public function addRemoval($id)
    {
        $this->removalIds[] = $id;
    }

    private function clearInserts()
    {
        $this->inserts = [];
    }

    private function clearRemovals()
    {
        $this->removalIds = [];
    }

    public function executeInserts()
    {
        if (sizeof($this->inserts) == 1)
        {
            return $this->executeSingleInsert();
        }

        return $this->executeBatchInsert();
    }

    private function executeSingleInsert()
    {
        $id = $this->queryBuilder->table($this->metadata->table())
            ->insert(reset($this->inserts));

        $oid = key($this->inserts);

        $this->clearInserts();

        return [$oid => $id];
    }

    private function executeBatchInsert()
    {
        $ids = $this->queryBuilder->table($this->metadata->table())
            ->insertMany($this->inserts);

        $this->clearInserts();

        return $ids;
    }

    public function executeRemovals()
    {
        if (sizeof($this->removalIds) == 1)
        {
            $this->executeSingleRemoval();
            return;
        }

        $this->executeBatchRemoval();
    }

    private function executeSingleRemoval()
    {
        $this->queryBuilder->table($this->metadata->table())
            ->where($this->metadata->primaryKey()->name(), '=', reset($this->removalIds))
            ->delete();

        $this->clearRemovals();
    }

    private function executeBatchRemoval()
    {
        $this->queryBuilder->table($this->metadata->table())
            ->where($this->metadata->primaryKey()->name(), 'in', '('.implode(',', $this->removalIds).')')
            ->delete();

        $this->clearRemovals();
    }
}
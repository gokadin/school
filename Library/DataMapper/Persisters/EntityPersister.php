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
        $id = $this->queryBuilder()->table($this->metadata->table())
            ->insert($this->inserts[0]);

        return [key($this->inserts) => $id];
    }

    private function executeBatchInsert()
    {
        $ids = $this->queryBuilder->table($this->metadata->table())
            ->insertMany(inserts);

        $result = [];
        for ($i = 0; $i < sizeof($ids); $i++)
        {
            $result[key($this->inserts[$i])] = $ids[$i];
        }

        return $result;
    }

    public function executeRemovals()
    {
        if (sizeof($this->removalIds) == 1)
        {
            $this->executeSingleRemoval();
        }

        $this->executeBatchRemoval();
    }

    private function executeSingleRemoval()
    {
        $this->queryBuilder()->table($this->metadata->table())
            ->where($this->metadata->primaryKey()->name(), '=', $this->removalIds[0])
            ->delete();
    }

    private function executeBatchRemoval()
    {
        $this->queryBuilder()->table($this->metadata->table())
            ->where($this->metadata->primaryKey()->name(), 'in', '('.implode(',', $this->removalIds).')')
            ->delete();
    }
}
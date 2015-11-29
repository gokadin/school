<?php

namespace Library\DataMapper\Persisters;

class BatchEntityPersister extends BasePersister
{
    public function executeRemovals(array $ids)
    {
        $metadata = $this->dm->getMetadata($this->class);

        $this->dm->queryBuilder()->table($metadata->table())
            ->where($metadata->primaryKey()->name(), 'in', '('.implode(',', $ids).')')
            ->delete();

        $this->finish();
    }
}
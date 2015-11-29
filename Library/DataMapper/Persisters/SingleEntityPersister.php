<?php

namespace Library\DataMapper\Persisters;

class SingleEntityPersister extends BasePersister
{
    public function executeRemoval($id)
    {
        $metadata = $this->dm->getMetadata($this->class);

        $this->dm->queryBuilder()->table($metadata->table())
            ->where($metadata->primaryKey()->name(), '=', $id)
            ->delete();

        $this->finish();
    }
}
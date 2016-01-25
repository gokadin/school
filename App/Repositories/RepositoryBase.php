<?php

namespace App\Repositories;

use Library\DataMapper\DataMapper;
use Library\Log\Log;

abstract class RepositoryBase
{
    /**
     * @var DataMapper
     */
    protected $dm;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @var string
     */
    protected $class;

    public function __construct(DataMapper $dm, Log $log, $class)
    {
        $this->dm = $dm;
        $this->log = $log;
        $this->class = $class;
    }

    public function find($id)
    {
        return $this->dm->find($this->class, $id);
    }

    public function findIn(array $ids)
    {
        return $this->dm->findIn($this->class, $ids);
    }

    public function delete($entity)
    {
        $this->dm->delete($entity);

        $this->dm->flush();
    }

    public function update($entity)
    {
        $this->dm->flush();
    }
}
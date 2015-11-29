<?php

namespace Library\DataMapper\Persisters;

use Library\DataMapper\DataMapper;
use Library\DataMapper\UnitOfWork\UnitOfWork;

/**
 * Responsible for the interactions between entities
 * of one class and the database.
 */
abstract class BasePersister
{
    /**
     * The data mapper instance.
     *
     * @var DataMapper
     */
    protected $dm;

    /**
     * The unit of work.
     *
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * The class the persister is responsible for.
     *
     * @var string
     */
    protected $class;

    /**
     * @param DataMapper $dm
     * @param UnitOfWork $unitOfWork
     */
    public function __construct(DataMapper $dm, UnitOfWork $unitOfWork)
    {
        $this->dm = $dm;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * Sets the class the persister will be working on.
     *
     * @param $class
     * @return $this
     */
    public function of($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Performs the final operations common to all execute functions.
     */
    public function finish()
    {
        $this->class = null;
    }
}
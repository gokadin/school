<?php

namespace Tests\FrameworkTest\TestData\DataMapper;

use Library\DataMapper\DataMapperPrimaryKey;

/** @Entity */
class LazyEntityTwo
{
    use DataMapperPrimaryKey;

    /** @Column(type="string") */
    private $name;

    /** @BelongsTo(target="Tests\FrameworkTest\TestData\DataMapper\LazyEntityOne") */
    private $entityOne;

    public function __construct($name, LazyEntityOne $entityOne = null)
    {
        $this->name = $name;
        $this->entityOne = $entityOne;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function entityOne()
    {
        return $this->entityOne;
    }

    public function setEntityOne(LazyEntityOne $entityOne)
    {
        $this->entityOne = $entityOne;
    }
}
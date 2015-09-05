<?php

namespace FrameworkTest\TestData\Database\DataMapper;

/**
 * @Entity(name="simpleEntity")
 */
class SimpleEntity
{
    /** @Id */
    protected $id;

    /** @Column(type="integer", indexed="true") */
    protected $one;

    /** @Column(name="customName", type="integer", size="12") */
    protected $two;

    public function __construct($one, $two)
    {
        $this->one = $one;
        $this->two = $two;
    }

    public function one()
    {
        return $this->one;
    }

    public function setOne($one)
    {
        $this->one = $one;
    }

    public function two()
    {
        return $this->two;
    }

    public function setTwo($two)
    {
        $this->two = $two;
    }
}
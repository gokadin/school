<?php

namespace App\Domain\School;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="schools")
 */
class School
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string") */
    protected $name;

    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    public function __construct($name, $address)
    {
        $this->name = $name;
        $this->address = $address;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }
}
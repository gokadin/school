<?php

namespace Tests\FrameworkTest\TestData\DataMapper;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;
use Library\DataMapper\Collection\EntityCollection;

/** @Entity */
class Teacher
{
    use DataMapperTimestamps, DataMapperPrimaryKey;

    /** @Column(type="string", size="50") */
    protected $name;

    /** @HasMany(target="Tests\FrameworkTest\TestData\DataMapper\Student", mappedBy="teacher") */
    protected $students;

    /** @HasOne(target="Tests\FrameworkTest\TestData\DataMapper\Address", cascade="delete, touch", nullable="true") */
    protected $address;

    public function __construct($name)
    {
        $this->name = $name;
        $this->students = new EntityCollection();
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function name()
    {
        return $this->name;
    }

    public function addStudent(Student $student)
    {
        $this->students->add($student);
    }

    public function removeStudent(Student $student)
    {
        $this->students->remove($student);
    }

    public function students()
    {
        return $this->students;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function removeAddress()
    {
        $this->address = null;
    }
}
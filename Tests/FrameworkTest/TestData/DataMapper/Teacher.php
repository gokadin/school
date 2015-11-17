<?php

namespace Tests\FrameworkTest\TestData\DataMapper;

use Library\DataMapper\DataMapperTimestamps;
use Library\DataMapper\Collection\EntityCollection;

/** @Entity */
class Teacher
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

    /** @Column(type="string", size="50") */
    protected $name;

    /** @HasMany(target="Tests\FrameworkTest\TestData\DataMapper\Student", mappedBy="teacher") */
    protected $students;

    public function __construct($name)
    {
        $this->name = $name;
        $this->students = new EntityCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function students()
    {
        return $this->students;
    }
}
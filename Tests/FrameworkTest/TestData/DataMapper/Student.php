<?php

namespace Tests\FrameworkTest\TestData\DataMapper;

use Library\DataMapper\DataMapperTimestamps;

/** @Entity */
class Student
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

    /** @Column(type="string", size="50") */
    protected $name;

    /** @BelongsTo(target="Tests\FrameworkTest\TestData\DataMapper\Teacher") */
    protected $teacher;

    public function __construct($name, Teacher $teacher)
    {
        $this->name = $name;
        $this->teacher = $teacher;
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

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function teacher()
    {
        return $this->teacher;
    }
}
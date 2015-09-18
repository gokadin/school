<?php

namespace FrameworkTest\TestData\DataMapper;

use Tests\FrameworkTest\Models\Teacher;

/**
 * @Entity
 */
class Student
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

    /** @Column(type="string", size="50") */
    protected $name;

    /** @BelongsTo(target="Tests\FrameworkTest\TestData\DataMapper\Teacher") */
    protected $teacher;

    public function __construct($name)
    {
        $this->name = $name;
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
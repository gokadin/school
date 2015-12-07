<?php

namespace App\Domain\Activities;

use App\Domain\Users\Teacher;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="activities")
 */
class Activity
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="decimal", size="3", precision="2") */
    protected $rate;

    /** @Column(type="string") */
    protected $period;

    /** @Column(type="string", nullable) */
    protected $location;

    /** @BelongsTo(target="\App\Domain\Users\Teacher") */
    protected $teacher;

    /** @HasMany(target="\App\Domain\Users\Student", mappedBy="activity") */
    protected $students;

    public function __construct(Teacher $teacher, $name, $rate, $period)
    {
        $this->teacher = $teacher;
        $this->name = $name;
        $this->rate = $rate;
        $this->period = $period;
    }

    public function name()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function rate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    public function period()
    {
        return $this->period;
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function location()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function students()
    {
        return $this->students;
    }
}
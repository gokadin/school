<?php

namespace App\Domain\Calendar;

use App\Domain\Users\Teacher;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="availabilities")
 */
class Availability
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @BelongsTo(target="\App\Domain\Users\Teacher") */
    private $teacher;

    /** @Column(type="datetime") */
    private $date;

    /** @Column(type="integer", size="3") */
    private $startTime;

    /** @Column(type="integer", size="3") */
    private $endTime;

    public function __construct(Teacher $teacher, $date, $startTime, $endTime)
    {
        $this->teacher = $teacher;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function date()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function startTime()
    {
        return $this->startTime;
    }

    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    public function endTime()
    {
        return $this->endTime;
    }

    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }
}
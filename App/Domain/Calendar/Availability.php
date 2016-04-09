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
    private $startDate;

    /** @Column(type="datetime") */
    private $endDate;

    /** @Column(type="integer", size="3") */
    private $startTime;

    /** @Column(type="integer", size="3") */
    private $endTime;

    public function __construct(Teacher $teacher, $startDate, $endDate, $startTime, $endTime)
    {
        $this->teacher = $teacher;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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

    public function startDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function endDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
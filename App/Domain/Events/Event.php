<?php

namespace App\Domain\Events;

use App\Domain\Users\Teacher;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="events")
 */
class Event
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @BelongsTo(target="\App\Domain\Users\Teacher") */
    private $teacher;

    /** @Column(type="string") */
    private $title;

    /** @Column(type="string", nullable) */
    private $description;

    /** @Column(type="datetime") */
    private $startDate;

    /** @Column(type="datetime") */
    private $endDate;

    /** @Column(type="string") */
    private $startTime;

    /** @Column(type="string") */
    private $endTime;

    /** @Column(type="boolean", default="true") */
    private $isAllDay;

    /** @Column(type="string") */
    private $color;

    /** @HasOne(target="\App\Domain\Activities\Activity", nullable) */
    private $activity;

    public function __construct($title, $description, $startDate, $endDate, $startTime, $endTime, $isAllDay, $color,
                                Teacher $teacher, $activity)
    {
        $this->teacher = $teacher;
        $this->title = $title;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->isAllDay = $isAllDay;
        $this->color = $color;
        $this->activity = $activity;
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->taacher = $teacher;
    }

    public function title()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function description()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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

    public function isAllDay()
    {
        return $this->isAllDay;
    }

    public function setIsAllDay($isAllDay)
    {
        $this->isAllDay = $isAllDay;
    }

    public function color()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function activity()
    {
        return $this->activity;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;
    }
}
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

    /** @Column(type="datetime") */
    private $startDate;

    /** @Column(type="datetime") */
    private $endDate;

    /** @Column(type="string") */
    private $color;

    public function __construct($title, $startDate, $endDate, $color, Teacher $teacher)
    {
        $this->teacher = $teacher;
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->color = $color;
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

    public function color()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }


}
<?php

namespace App\Domain\Events;

use App\Domain\Users\Student;
use Carbon\Carbon;
use Library\DataMapper\DataMapperPrimaryKey;

/** @Entity(name="lessons") */
class Lesson
{
    use DataMapperPrimaryKey;

    private $decodedMissedDates;

    /** @BelongsTo(target="\App\Domain\Events\Event") */
    private $event;

    /** @BelongsTo(target="\App\Domain\Users\Student") */
    private $student;

    /** @Column(type="text", nullable) */
    private $missedDates;

    /** @Column(type="datetime") */
    private $absoluteStart;

    /** @Column(type="datetime") */
    private $absoluteEnd;

    public function __construct($event, $student, $absoluteStart, $absoluteEnd)
    {
        $this->event = $event;
        $this->student = $student;
        $this->absoluteStart = $absoluteStart;
        $this->absoluteEnd = $absoluteEnd;
    }

    public function event()
    {
        return $this->event;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    public function student()
    {
        return $this->student;
    }

    public function setStudent(Student $student)
    {
        $this->student = $student;
    }

    public function absoluteStart()
    {
        return $this->absoluteStart;
    }

    public function setAbsoluteStart($absoluteStart)
    {
        $this->absoluteStart = $absoluteStart;
    }

    public function absoluteEnd()
    {
        return $this->absoluteEnd;
    }

    public function setAbsoluteEnd($absoluteEnd)
    {
        $this->absoluteEnd = $absoluteEnd;
    }

    public function miss(Carbon $date)
    {
        $missedDates = $this->missedDates();
        $missedDates[] = $date->toDateString();
        $this->decodedMissedDates = $missedDates;

        $this->missedDates = json_encode($missedDates);
    }

    public function missedDates()
    {
        if (is_null($this->decodedMissedDates))
        {
            $decoded = json_decode($this->missedDates, true);
            $this->decodedMissedDates = is_null($decoded) ? [] : $decoded;
        }

        return $this->decodedMissedDates;
    }
}
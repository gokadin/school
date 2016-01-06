<?php

namespace App\Domain\Events;

use App\Domain\Users\Student;
use Library\DataMapper\DataMapperPrimaryKey;

/** @Entity(name="lessons") */
class Lesson
{
    use DataMapperPrimaryKey;

    /** @BelongsTo(target="\App\Domain\Events\Event") */
    private $event;

    /** @BelongsTo(target="\App\Domain\Users\Student") */
    private $student;

    /** @Column(type="boolean", default="true") */
    private $hasAttended;

    public function __construct($event, $student)
    {
        $this->event = $event;
        $this->student = $student;
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

    public function hasAttended()
    {
        return $this->hasAttended;
    }

    public function setHasAttended($hasAttended)
    {
        $this->hasAttended = $hasAttended;
    }
}
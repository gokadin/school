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

    /** @HasMany(target="\App\Domain\Events\Lesson", mappedBy="event") */
    private $lessons;

    /** @Column(type="boolean") */
    private $isRecurring;

    /** @Column(type="string") */
    private $rRepeat;

    /** @Column(type="string") */
    private $rEvery;

    /** @Column(type="datetime") */
    private $rEndDate;

    /** @Column(type="boolean") */
    private $rEndsNever;

    /** @Column(type="string") */
    private $location;

    /** @Column(type="string") */
    private $visibility;

    /** @Column(type="string") */
    private $notifyMeBy;

    /** @Column(type="string") */
    private $notifyMeBefore;

    /** @Column(type="datetime") */
    private $absoluteStart;

    /** @Column(type="datetime") */
    private $absoluteEnd;

    public function __construct($title, $description, $startDate, $endDate, $startTime, $endTime, $isAllDay, $color,
                                Teacher $teacher, $activity, $isRecurring, $rRepeat, $rEvery, $rEndDate, $rEndsNever,
                                $location, $visibility, $notifyMeBy, $notifyMeBefore, $absoluteStart, $absoluteEnd)
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
        $this->isRecurring = $isRecurring;
        $this->rRepeat = $rRepeat;
        $this->rEvery = $rEvery;
        $this->rEndDate = $rEndDate;
        $this->rEndsNever = $rEndsNever;
        $this->location = $location;
        $this->visibility = $visibility;
        $this->notifyMeBy = $notifyMeBy;
        $this->notifyMeBefore = $notifyMeBefore;
        $this->absoluteStart = $absoluteStart;
        $this->absoluteEnd = $absoluteEnd;
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

    public function lessons()
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson)
    {
        $this->lessons->add($lesson);
    }

    public function removeLesson(Lesson $lesson)
    {
        $this->lessons->remove($lesson);
    }

    public function isRecurring()
    {
        return $this->isRecurring;
    }

    public function setIsRecurring($isRecurring)
    {
        $this->isRecurring = $isRecurring;
    }

    public function rRepeat()
    {
        return $this->rRepeat;
    }

    public function setRRepeat($rRepeat)
    {
        $this->rRepeat = $rRepeat;
    }

    public function rEvery()
    {
        return $this->rEvery;
    }

    public function setREvery($rEvery)
    {
        $this->rEvery = $rEvery;
    }

    public function rEndDate()
    {
        return $this->rEndDate;
    }

    public function setREndDate($rEndDate)
    {
        $this->rEndDate = $rEndDate;
    }

    public function rEndsNever()
    {
        return $this->rEndsNever;
    }

    public function setREndsNever($rEndsNever)
    {
        $this->rEndsNever = $rEndsNever;
    }

    public function location()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function visibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    public function notifyMeBy()
    {
        return $this->notifyMeBy;
    }

    public function setNotifyMeBy($notifyMeBy)
    {
        $this->notifyMeBy = $notifyMeBy;
    }

    public function notifyMeBefore()
    {
        return $this->notifyMeBefore;
    }

    public function setNotifyMeBefore($notifyMeBefore)
    {
        $this->notifyMeBefore = $notifyMeBefore;
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
}
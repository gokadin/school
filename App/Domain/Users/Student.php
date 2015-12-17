<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use App\Domain\Common\Address;

/**
 * @Entity(name="students")
 */
class Student extends User
{
    /** @HasOne(target="App\Domain\Activities\Activity") */
    protected $activity;

    /** @BelongsTo(target="App\Domain\Users\Teacher") */
    private $teacher;

    /** @Column(type="text", nullable) */
    private $extraInfo;

    public function __construct($firstName, $lastName, $email, $password, Address $address,
                                Activity $activity, Teacher $teacher)
    {
        parent::__construct($firstName, $lastName, $email, $password, $address);

        $this->activity = $activity;
        $this->teacher = $teacher;
    }

    public function activity()
    {
        return $this->activity;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function extraInfo()
    {
        return json_decode($this->extraInfo, true);
    }

    public function setExtraInfo($extraInfo)
    {
        $this->extraInfo = json_encode($extraInfo);
    }
}
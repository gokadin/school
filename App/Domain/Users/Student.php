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

    /** @Column(type="boolean", default="true") */
    private $active;

    /** @Column(type="string", nullable) */
    private $gender;

    /** @Column(type="datetime", nullable) */
    private $dateOfBirth;

    /** @Column(type="string", nullable) */
    private $occupation;

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

    public function active()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function gender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    public function dateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function occupation()
    {
        return $this->occupation;
    }

    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    }
}
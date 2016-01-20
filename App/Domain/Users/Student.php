<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use App\Domain\Common\Address;
use App\Domain\Events\Lesson;
use App\Domain\Setting\StudentSettings;
use Library\DataMapper\Collection\EntityCollection;

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

    /** @Column(type="decimal", size="5", precision="2") */
    private $customPrice;

    /** @Column(type="boolean") */
    private $hasAccount;

    /** @HasMany(target="App\Domain\Events\Lesson", mappedBy="student") */
    private $lessons;

    /** @HasOne(target="App\Domain\Setting\StudentSettings") */
    private $settings;

    public function __construct($firstName, $lastName, $email, $password, Address $address,
                                Activity $activity, $customPrice, $hasAccount, Teacher $teacher,
                                StudentSettings $settings)
    {
        parent::__construct($firstName, $lastName, $email, $password, $address, $teacher->school());

        $this->activity = $activity;
        $this->customPrice = $customPrice;
        $this->hasAccount = $hasAccount;
        $this->teacher = $teacher;
        $this->settings = $settings;

        $this->lessons = new EntityCollection();
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

    public function customPrice()
    {
        return $this->customPrice;
    }

    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
    }

    public function hasAccount()
    {
        return $this->hasAccount;
    }

    public function setHasAccount($hasAccount)
    {
        $this->hasAccount = $hasAccount;
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

    public function settings()
    {
        return $this->settings;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }
}
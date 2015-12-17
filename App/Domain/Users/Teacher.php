<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use App\Domain\Setting\TeacherSettings;
use Library\DataMapper\Collection\EntityCollection;
use App\Domain\Common\Address;

/**
 * @Entity(name="teachers")
 */
class Teacher extends User
{
    /** @HasOne(target="App\Domain\Subscriptions\Subscription") */
    protected $subscription;

    /** @HasOne(target="App\Domain\School\School") */
    protected $school;

    /** @HasOne(target="App\Domain\Setting\TeacherSettings") */
    private $settings;

    /** @HasMany(target="App\Domain\Activities\Activity", mappedBy="activity") */
    protected $activities;

    /** @HasMany(target="App\Domain\Users\Student", mappedBy="teacher") */
    private $students;

    public function __construct($firstName, $lastName, $email, $password, $subscription,
                                Address $address, $school, TeacherSettings $settings)
    {
        parent::__construct($firstName, $lastName, $email, $password, $address);

        $this->subscription = $subscription;
        $this->school = $school;
        $this->settings = $settings;
        $this->activities = new EntityCollection();
        $this->students = new EntityCollection();
    }

    public function subscription()
    {
        return $this->subscription;
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    public function school()
    {
        return $this->school;
    }

    public function setSchool($school)
    {
        $this->school = $school;
    }

    public function settings()
    {
        return $this->settings;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return PersistentCollection
     */
    public function activities()
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity)
    {
        $this->activities->add($activity);
    }

    public function removeActivity(Activity $activity)
    {
        $this->activities->remove($activity);
    }

    /**
     * @return PersistentCollection
     */
    public function students()
    {
        return $this->students;
    }

    public function addStudent(Student $student)
    {
        $this->students->add($student);
    }

    public function removeStudent(Student $student)
    {
        $this->students->remove($student);
    }
}
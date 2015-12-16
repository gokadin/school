<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use App\Domain\Setting\TeacherSettings;
use Library\DataMapper\Collection\EntityCollection;

/**
 * @Entity(name="teachers")
 */
class Teacher extends User
{
    /** @Column(type="string") */
    protected $firstName;

    /** @Column(type="string") */
    protected $lastName;

    /** @HasOne(target="App\Domain\Subscriptions\Subscription") */
    protected $subscription;

    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    /** @HasOne(target="App\Domain\School\School") */
    protected $school;

    /** @HasOne(target="App\Domain\Setting\TeacherSettings") */
    private $settings;

    /** @HasMany(target="App\Domain\Activities\Activity", mappedBy="activity") */
    protected $activities;

    public function __construct($firstName, $lastName, $email, $password, $subscription,
                                $address, $school, TeacherSettings $settings)
    {
        parent::__construct($email);

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
        $this->subscription = $subscription;
        $this->address = $address;
        $this->school = $school;
        $this->settings = $settings;
        $this->activities = new EntityCollection();
    }

    public function firstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function lastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function name()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function subscription()
    {
        return $this->subscription;
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
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
}
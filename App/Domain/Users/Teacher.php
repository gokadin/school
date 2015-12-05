<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use Library\DataMapper\Collection\EntityCollection;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="teachers")
 */
class Teacher
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string") */
    protected $firstName;

    /** @Column(type="string") */
    protected $lastName;

    /** @Column(type="string") */
    protected $email;

    /** @Column(type="string") */
    protected $password;

    /** @HasOne(target="App\Domain\Subscriptions\Subscription") */
    protected $subscription;

    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    /** @HasOne(target="App\Domain\School\School") */
    protected $school;

    /** @HasMany(target="App\Domain\Activities\Activity", mappedBy="activity") */
    protected $activities;

    public function __construct($firstName, $lastName, $email, $password, $subscription, $address, $school)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->subscription = $subscription;
        $this->address = $address;
        $this->school = $school;
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

    public function email()
    {
        return $this->email;
    }

    public function password()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
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
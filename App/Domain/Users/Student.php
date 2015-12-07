<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use Library\DataMapper\DataMapperTimestamps;
use Library\DataMapper\DataMapperPrimaryKey;

/**
 * @Entity(name="students")
 */
class Student
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string") */
    protected $email;

    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    /** @HasOne(target="App\Domain\Activities\Activity") */
    private $activity;

    public function __construct($email, Activity $activity)
    {
        $this->email = $email;
        $this->activity = $activity;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function email()
    {
        return $this->email;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function activity()
    {
        return $this->activity;
    }

    public function setActivity($activity)
    {
        $this->activity = $activity;
    }
}
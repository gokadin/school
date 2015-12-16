<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;

/**
 * @Entity(name="students")
 */
class Student extends User
{
    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    /** @HasOne(target="App\Domain\Activities\Activity") */
    protected $activity;

    public function __construct($email, Activity $activity)
    {
        parent::__construct($email);

        $this->activity = $activity;
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
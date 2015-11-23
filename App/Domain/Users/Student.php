<?php

namespace App\Domain\Users;

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

    public function __construct($email)
    {
        $this->email = $email;
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
}
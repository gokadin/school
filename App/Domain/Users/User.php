<?php

namespace App\Domain\Users;

use App\Domain\Common\Address;
use App\Domain\School\School;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

class User
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

    /** @HasOne(target="App\Domain\Common\Address") */
    protected $address;

    /** @HasOne(target="App\Domain\School\School") */
    protected $school;

    public function __construct($firstName, $lastName, $email, $password, Address $address, School $school)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->address = $address;
        $this->school = $school;
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

    public function email()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function password()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function address()
    {
        return $this->address;
    }

    public function setAddress(Address $address)
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
}
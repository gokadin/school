<?php

namespace App\Domain\Users;

/**
 * @Entity(name="teachers")
 */
class Teacher
{
    /** @Id */
    protected $id;

    /** @Column(type="string") */
    protected $firstName;

    /** @Column(type="string") */
    protected $lastName;

    /** @Column(type="string") */
    protected $email;

    public function firstName()
    {
        return $this->firstName;
    }

    public function __construct($firstName, $lastName, $email)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
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

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function name()
    {
        return $this->firstName.' '.$this->lastName;
    }
}
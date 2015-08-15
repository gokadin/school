<?php

namespace App\Domain\Users;

/**
 * @Entity(name="teacher")
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

    public function lastName()
    {
        return $this->lastName;
    }

    public function email()
    {
        return $this->email;
    }

    public function name()
    {
        return $this->firstName.' '.$this->lastName;
    }
}
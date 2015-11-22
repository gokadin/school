<?php

namespace App\Domain\Users;

use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="students")
 */
class Student
{
    use DataMapperTimestamps;

    /** @Id */
    protected $id;

    /** @Column(type="string") */
    protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function email()
    {
        return $this->email;
    }
}
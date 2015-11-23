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
}
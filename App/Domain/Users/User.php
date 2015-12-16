<?php

namespace App\Domain\Users;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

class User
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string") */
    protected $email;

    /** @Column(type="string") */
    protected $password;

    public function __construct($email)
    {
        $this->email = $email;
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
}
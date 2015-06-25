<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Teacher extends Model
{
    public function students()
    {
        return $this->hasMany('Student');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }
}
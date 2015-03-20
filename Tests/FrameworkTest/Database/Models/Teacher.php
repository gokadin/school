<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class Teacher extends Model
{
    public function students()
    {
        return $this->hasMany('Student');
    }
}
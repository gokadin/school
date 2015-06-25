<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Student extends Model
{
    public function teacher()
    {
        return $this->hasOne('Teacher');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function activities()
    {
        return $this->belongsToMany('Activity');
    }
}
<?php namespace Models;

use Library\Database\Model;

class Student extends Model
{
    public function user()
    {
        return $this->morphOne('User');
    }

    public function activities()
    {
        return $this->hasMany('Activity');
    }

    public function school()
    {
        return $this->hasOne('School');
    }
}
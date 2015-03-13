<?php namespace Models;

use Library\Database\Model;

class Teacher extends Model
{
    protected $inheritsFrom = 'User';

    public function user()
    {
        return $this->morphOne('User');
    }

    public function school()
    {
        return $this->hasOne('School');
    }

    public function activities()
    {
        return $this->hasMany('Activity');
    }
}
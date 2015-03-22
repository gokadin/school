<?php namespace Models;

use Library\Database\Model;

class Student extends Model
{
    protected $inheritsFrom = 'User';

    public function user()
    {
        return $this->morphOne('User');
    }

    public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public function school()
    {
        return $this->hasOne('School');
    }
}
<?php namespace Models;

use Library\Database\Model;

class Student extends Model
{
    public function userInfo()
    {
        return $this->hasOne('UserInfo');
    }

    public function activities()
    {
        return $this->belongsToMany('Activity');
    }
}
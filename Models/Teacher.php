<?php namespace Models;

use Library\Database\Model;

class Teacher extends Model
{
    public function userInfo()
    {
        return $this->hasOne('UserInfo');
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
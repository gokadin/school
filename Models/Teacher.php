<?php namespace Models;

use Library\Database\Model;

class Teacher extends Model
{
    public function userInfo()
    {
        return $this->hasOne('UserInfo');
    }

    public function subscription()
    {
        return $this->hasOne('Subscription');
    }

    public function students()
    {
        return $this->hasMany('Student');
    }

    public function activities()
    {
        return $this->hasMany('Activity');
    }
}
<?php namespace Models;

use Library\Database\Model;

class Student extends Model
{
    public function school()
    {
        return $this->hasOne('School');
    }

    public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function settings()
    {
        return $this->hasOne('StudentSetting');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
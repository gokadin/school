<?php namespace Models;

use Library\Database\Model;

class UserInfo extends Model
{
	public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function student()
    {
        return $this->belongsTo('Student');
    }

    public function school()
    {
        return $this->hasOne('School');
    }

    public function userSettings()
    {
        return $this->hasOne('UserSetting');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function events()
    {
        return $this->hasMany('Event');
    }
}

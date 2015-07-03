<?php namespace Models;

use Library\Database\Model;

class Teacher extends Model
{
    protected $fillable = [
        'subscription_id',
        'address_id',
        'teacher_setting_id',
        'school_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'type',
        'active',
        'profile_picture'
    ];

    public function school()
    {
        return $this->hasOne('School');
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

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function settings()
    {
        return $this->hasOne('TeacherSetting');
    }

    public function events()
    {
        return $this->hasMany('TeacherEvent');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
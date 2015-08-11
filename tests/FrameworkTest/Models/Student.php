<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Student extends Model
{
    protected $fillable = [
        'address_id',
        'school_id',
        'teacher_id',
        'name'
    ];

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
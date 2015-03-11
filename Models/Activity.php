<?php namespace Models;

use Library\Database\Model;

class Activity extends Model
{
    public function activities()
    {
        return $this->hasMany('Student');
    }

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}
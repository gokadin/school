<?php namespace Models;

use Library\Database\Model;

class Activity extends Model
{
    public function students()
    {
        return $this->belongsToMany('Student');
    }

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}
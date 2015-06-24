<?php namespace Models;

use Library\Database\Model;

class StudentSetting extends Model
{
    public function student()
    {
        return $this->belongsTo('Student');
    }
}
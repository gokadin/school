<?php namespace Models;

use Library\Database\Model;

class TeacherSetting extends Model
{
    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}
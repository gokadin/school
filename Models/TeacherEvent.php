<?php namespace Models;

use Library\Database\Model;

class TeacherEvent extends Model
{
    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}

<?php namespace Models;

use Library\Database\Model;

class User extends Model
{
    public function school()
    {
        return $this->hasOne('School');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
<?php namespace Models;

use Library\Database\Model;

class School extends Model
{
    public function users()
    {
        return $this->hasMany('User');
    }
}
<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class Activity extends Model
{
    public function students()
    {
        return $this->belongsToMany('Student');
    }
}
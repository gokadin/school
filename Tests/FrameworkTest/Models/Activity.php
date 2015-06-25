<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Activity extends Model
{
    public function students()
    {
        return $this->belongsToMany('Student');
    }
}
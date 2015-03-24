<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class School extends Model
{
    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function posts()
    {
        return $this->hasManyThrough('Post', 'Student');
    }
}
<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Address extends Model
{
    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function student()
    {
        return $this->belongsTo('Student');
    }
}
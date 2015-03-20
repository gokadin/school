<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class Student extends Model
{
    public function teacher()
    {
        return $this->hasOne('Teacher');
    }
}
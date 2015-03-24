<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class Animal extends Model
{
    public function morph()
    {
        return $this->morphTo();
    }

    public function uppercaseCol1()
    {
        return strtoupper($this->animalCol1);
    }
}
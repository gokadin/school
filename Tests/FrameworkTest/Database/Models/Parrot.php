<?php namespace Tests\FrameworkTest\Database\Models;

use Library\Database\Model;

class Parrot extends Model
{
    protected $inheritsFrom = 'Animal';

    public function animal()
    {
        return $this->morphOne('Animal');
    }
}
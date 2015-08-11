<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'rate'
    ];

    public function students()
    {
        return $this->belongsToMany('Student');
    }
}
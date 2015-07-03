<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Address extends Model
{
    protected $fillable = [
        'country',
        'city',
        'postal',
        'civic',
        'street',
        'app'
    ];

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function student()
    {
        return $this->belongsTo('Student');
    }
}
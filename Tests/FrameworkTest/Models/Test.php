<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Test extends Model
{
    protected $fillable = [
        'col1',
        'col2'
    ];

    public function getAccessorTestAttribute()
    {
        return 'test';
    }
}
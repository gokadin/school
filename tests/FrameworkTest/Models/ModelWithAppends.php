<?php

namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class ModelWithAppends extends Model
{
    protected $fillable = [
        'col1'
    ];

    protected $appends = [
        'appendedValue'
    ];

    public function getAppendedValueAttribute()
    {
        return 'test';
    }
}
<?php namespace Models;

use Library\Database\Model;

class ActivityStudent extends Model
{
    protected $fillable = [
        'activity_id',
        'student_id',
        'rate'
    ];
}
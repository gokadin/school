<?php namespace Models;

use Library\Database\Model;

class StudentMessageOut extends Model
{
    protected $fillable = [
        'student_id',
        'to_id',
        'to_type',
        'content',
        'is_read'
    ];
}
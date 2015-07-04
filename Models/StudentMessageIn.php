<?php namespace Models;

use Library\Database\Model;

class StudentMessageIn extends Model
{
    protected $fillable = [
        'student_id',
        'from_id',
        'from_type',
        'content',
        'is_read'
    ];
}
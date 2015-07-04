<?php namespace Models;

use Library\Database\Model;

class TeacherMessageIn extends Model
{
    protected $fillable = [
        'teacher_id',
        'from_id',
        'from_type',
        'content',
        'is_read'
    ];
}
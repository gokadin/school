<?php namespace Models;

use Library\Database\Model;

class TeacherMessageOut extends Model
{
    protected $fillable = [
        'teacher_id',
        'to_id',
        'to_type',
        'content',
        'is_read'
    ];
}
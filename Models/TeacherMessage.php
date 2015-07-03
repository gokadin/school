<?php namespace Models;

use Library\Database\Model;

class TeacherMessage extends Model
{
    protected $fillable = [
        'teacher_id',
        'recipient_id',
        'recipient_type',
        'subject',
        'content',
        'is_read'
    ];
}
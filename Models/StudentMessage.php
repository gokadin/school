<?php namespace Models;

use Library\Database\Model;

class StudentMessage extends Model
{
    protected $fillable = [
        'student_id',
        'recipient_id',
        'recipient_type',
        'subject',
        'content',
        'is_read'
    ];
}
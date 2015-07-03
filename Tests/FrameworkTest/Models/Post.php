<?php namespace Tests\FrameworkTest\Models;

use Library\Database\Model;

class Post extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'content'
    ];
}
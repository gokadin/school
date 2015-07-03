<?php namespace Models;

use Library\Database\Model;

class TeacherSetting extends Model
{
    protected $fillable = [
        'show_email',
        'show_address',
        'show_phone'
    ];

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}
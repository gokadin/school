<?php namespace Models;

use Library\Database\Model;

class StudentSetting extends Model
{
    protected $fillable = [
        'show_email',
        'show_address',
        'show_phone'
    ];

    public function student()
    {
        return $this->belongsTo('Student');
    }
}
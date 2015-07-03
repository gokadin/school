<?php namespace Models;

use Library\Database\Model;

class School extends Model
{
    protected $fillable = [
        'address_id',
        'name'
    ];

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function students()
    {
        return $this->hasMany('Student');
    }
}
<?php namespace Models;

use Library\Database\Model;

class Message extends Model
{
    public function user()
    {
        return $this->belongsTo('User')->morphTo();
    }
}

<?php namespace Models;

use Library\Database\Model;

class Address extends Model
{
    public function user_info()
    {
        return $this->belongsTo('UserInfo');
    }
}
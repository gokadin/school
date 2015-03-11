<?php namespace Models;

use Library\Database\Model;

class UserSetting extends Model
{
    public function user_info()
    {
        return $this->belongsTo('UserInfo');
    }
}
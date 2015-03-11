<?php namespace Models;

use Library\Database\Model;

class UserInfo extends Model
{
    public function address()
    {
        return $this->hasOne('Address');
    }

    public function userSetting()
    {
        return $this->hasOne('UserSetting');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
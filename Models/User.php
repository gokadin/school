<?php namespace Models;

use Library\Database\Model;

class User extends Model
{
    public function morph()
    {
        return $this->morphTo();
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function userSetting()
    {
        return $this->hasOne('UserSetting');
    }
    
    public function messagesReceived()
    {
        return Message::where('to_user_id')->get();
    }
    
    public function messagesSent()
    {
        return $this->hasMany('Message');
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
    
    public function hasProfilePicture()
    {
        return $this->profile_picture != null && 
            !empty($this->profile_picture);
    }
}
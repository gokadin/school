<?php

namespace Models;

use Library\Database\Model;
use Library\Database\ModelCollection;

class Teacher extends Model
{
    protected $fillable = [
        'subscription_id',
        'address_id',
        'teacher_setting_id',
        'school_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'type',
        'active',
        'profile_picture'
    ];

    public function school()
    {
        return $this->hasOne('School');
    }

    public function subscription()
    {
        return $this->hasOne('Subscription');
    }

    public function students()
    {
        return $this->hasMany('Student');
    }

    public function activities()
    {
        return $this->hasMany('Activity');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function settings()
    {
        return $this->hasOne('TeacherSetting');
    }

    public function messagesOut()
    {
        return $this->hasMany('TeacherMessageOut');
    }

    public function messagesIn()
    {
        return $this->hasMany('TeacherMessageIn');
    }

    public function events()
    {
        return $this->hasMany('TeacherEvent');
    }

    public function conversations()
    {
        $conversationIds = UserConversation::where('user_id', '=', $this->primaryKeyValue())
            ->where('user_type', '=', 'Teacher')
            ->get('conversation_id');

        if (is_null($conversationIds) || sizeof($conversationIds) == 0)
        {
            return new ModelCollection();
        }

        $conversations = Conversation::where('id', 'in', '('.implode(',', $conversationIds).')')->get();

        if (!($conversations instanceof ModelCollection))
        {
            return new ModelCollection([$conversations]);
        }

        return $conversations;
    }

    public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
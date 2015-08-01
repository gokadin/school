<?php namespace Models;

use Library\Database\Model;
use Library\Database\ModelCollection;

class Student extends Model
{
    protected $fillable = [
        'teacher_id',
        'address_id',
        'student_setting_id',
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

    public function activities()
    {
        return $this->belongsToMany('Activity');
    }

    public function address()
    {
        return $this->hasOne('Address');
    }

    public function settings()
    {
        return $this->hasOne('StudentSetting');
    }

    public function messagesOut()
    {
        return $this->hasMany('StudentMessageOut');
    }

    public function messagesIn()
    {
        return $this->hasMany('StudentMessageIn');
    }

    public function conversations()
    {
        $conversationIds = UserConversation::where('user_id', '=', $this->primaryKeyValue())
            ->where('user_type', '=', 'Student')
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
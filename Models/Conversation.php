<?php

namespace Models;

use Library\Database\Model;

class Conversation extends Model
{
    public function messages()
    {
        return $this->hasMany('ConversationMessage');
    }
}
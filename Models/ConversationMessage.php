<?php

namespace Models;

use Library\Database\Model;

class ConversationMessage extends Model
{
    public function conversation()
    {
        return $this->belongsTo('Conversation');
    }
}
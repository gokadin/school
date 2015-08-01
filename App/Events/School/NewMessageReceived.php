<?php

namespace App\Events\School;

use App\Events\Event;
use Library\Events\ShouldBroadcast;
use Library\Facades\Sentry;

class NewMessageReceived extends Event implements ShouldBroadcast
{
    protected $conversationId;
    protected $content;
    protected $created_at;

    public function __construct($conversationId, $content, $created_at)
    {
        $this->conversationId = $conversationId;
        $this->content = $content;
        $this->created_at = $created_at;
    }

    public function broadcastOn()
    {
        return [
            'messaging.newMessageReceived' => [
                'conversation_id' => $this->conversationId,
                'from_id' => Sentry::id(),
                'from_type' => Sentry::type(),
                'content' => $this->content,
                'created_at' => $this->created_at
            ]
        ];
    }
}
<?php

namespace App\Events\School;

use App\Events\Event;
use Library\Events\ShouldBroadcast;

class NewMessageReceived extends Event implements ShouldBroadcast
{
    protected $message;
    protected $toId;
    protected $toType;

    public function __construct($message, $toId, $toType)
    {
        $this->message = $message;
        $this->toId = $toId;
        $this->toType = $toType;
    }

    public function broadcastOn()
    {
        return [
            'messaging.newMessageReceived' => [
                'message' => $this->message,
                'toId' => $this->toId,
                'toType' => $this->toType
            ]
        ];
    }
}
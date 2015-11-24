<?php

namespace Library\Events;

use App\Events\Event;

trait FiresEvents
{
    protected function fireEvent(Event $event)
    {
        \Library\Facades\Event::fire($event);
    }
}
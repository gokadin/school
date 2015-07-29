<?php

namespace Tests\FrameworkTest\TestData\Queue;

use App\Events\Event;

class SimpleEvent extends Event
{
    protected $eventValue;

    public function __construct($eventValue)
    {
        $this->eventValue = $eventValue;
    }

    public function eventValue()
    {
        return $this->eventValue;
    }
}
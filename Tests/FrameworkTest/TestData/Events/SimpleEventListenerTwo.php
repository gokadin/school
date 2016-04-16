<?php

namespace Tests\FrameworkTest\TestData\Events;

use Library\Events\Listener;

class SimpleEventListenerTwo extends Listener
{
    public function handle(SimpleEvent $event)
    {
        $event->secondFired();
    }
}
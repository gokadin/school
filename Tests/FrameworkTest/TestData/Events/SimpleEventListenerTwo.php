<?php

namespace Tests\FrameworkTest\TestData\Events;

class SimpleEventListenerTwo
{
    public function handle(SimpleEvent $event)
    {
        $event->secondFired();
    }
}
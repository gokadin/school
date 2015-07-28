<?php

namespace Tests\FrameworkTest\TestData\Events;

class SimpleEventListener
{
    public function handle(SimpleEvent $event)
    {
        $event->fired();
    }
}
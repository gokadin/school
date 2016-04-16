<?php

namespace Tests\FrameworkTest\TestData\Events;

use Library\Events\Listener;

class SimpleEventListener extends Listener
{
    public function handle(SimpleEvent $event)
    {
        $event->fired();
    }
}
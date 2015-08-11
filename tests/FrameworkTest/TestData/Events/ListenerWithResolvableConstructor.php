<?php

namespace Tests\FrameworkTest\TestData\Events;

class ListenerWithResolvableConstructor
{
    protected $simpleEvent;

    public function __construct(SimpleEvent $simpleEvent)
    {
        $this->simpleEvent = $simpleEvent;
    }

    public function handle(EventTestingResolvableConstructor $event)
    {
        $event->fired();
        $event->setResolvedParameter($this->simpleEvent);
    }
}
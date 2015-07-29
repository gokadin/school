<?php

namespace Library\Events;

use Library\Facades\App;
use Library\Queue\ShouldQueue;

class EventManager
{
    protected $listeners;

    public function __construct()
    {
        $this->listeners = array();
    }

    /**
     * Register a listener to an event
     *
     * @param $event class name
     * @param array $listeners class name
     * @return void
     */
    public function register($event, array $listeners)
    {
        foreach ($listeners as $listener)
        {
            $this->listeners[$event][] = $listener;
        }
    }

    /**
     * Returns all listeners class names
     * associated with the event
     *
     * @param $event class name
     * @return array
     */
    public function getListeners($event)
    {
        if (!isset($this->listeners[$event]))
        {
            return [];
        }

        return $this->listeners[$event];
    }

    /**
     * Fires an event
     *
     * @param $event
     * @return void
     */
    public function fire($event)
    {
        $eventClassName = get_class($event);

        if (!isset($this->listeners[$eventClassName]))
        {
            return;
        }

        foreach ($this->listeners[$eventClassName] as $listener)
        {
            $resolvedListener = App::container()->resolve($listener);

            if ($resolvedListener instanceof ShouldQueue)
            {
                $this->pushToQueue($event, $resolvedListener);
                continue;
            }

            $resolvedListener->handle($event);
        }
    }

    /**
     * Pushes the listener on the queue
     *
     * @param $event
     * @param $listener
     */
    protected function pushToQueue($event, $listener)
    {
        App::container()->resolveInstance('queue')->push($event, $listener);
    }
}
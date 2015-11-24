<?php

namespace Library\Events;

use Library\Container\Container;
use Library\Queue\Queue;

class EventManager
{
    protected $listeners = [];
    protected $container;
    protected $queue;

    public function __construct($config, Container $container, Queue $queue)
    {
        $this->container = $container;
        $this->queue = $queue;

        $this->registerListeners($config);
    }

    protected function registerListeners($config)
    {
        foreach ($config as $eventClass => $listeners)
        {
            foreach ($listeners as $listenerClass)
            {
                $this->listeners[$eventClass][] = $listenerClass;
            }
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
        if ($event instanceof ShouldBroadcast)
        {
            $this->broadcastEvent($event);
        }

        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass]))
        {
            return;
        }

        foreach ($this->listeners[$eventClass] as $listenerClass)
        {
            $this->queue->push($event, $this->container->resolve($listenerClass));
        }
    }

    // **************************** NOT WORKING!!!!!
    /**
     * Broadcasts the event
     *
     * @param $event
     */
    protected function broadcastEvent($event)
    {
        $data = $event->broadcastOn();

        foreach ($data as $channel => $payload)
        {
            //$this->redis->publish($channel, json_encode($payload));
        }
    }
}
<?php

namespace Library\Patterns\Observer;

abstract class Observable
{
    /**
     * @var array
     */
    protected $subscribers = [];

    public function subscribe(Observer $observer)
    {
        $oid = spl_object_hash($observer);

        if (!isset($this->subscribers[$oid]))
        {
            $this->subscribers[$oid] = $observer;
        }
    }

    /**
     * @param ObservableEvent $event
     */
    protected function fireEvent(ObservableEvent $event)
    {
        foreach ($this->subscribers as $subscriber)
        {
            $subscriber->next($event);
        }
    }
}
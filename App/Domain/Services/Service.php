<?php

namespace App\Domain\Services;

use App\Repositories\Repository;
use Library\Events\Event;
use Library\Events\EventManager;

abstract class Service
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(EventManager $eventManager, Repository $repository)
    {
        $this->eventManager = $eventManager;
        $this->repository = $repository;
    }

    /**
     * @param Event $event
     */
    protected function fireEvent(Event $event)
    {
        $this->eventManager->fire($event);
    }
}
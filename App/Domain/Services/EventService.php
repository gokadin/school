<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class EventService extends AuthenticatedService
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                UserRepository $userRepository, EventRepository $eventRepository)
    {
        parent::__construct($queue, $eventManager, $transformer, $userRepository);

        $this->eventRepository = $eventRepository;
    }

    public function create(array $data)
    {
        $data['teacher'] = $this->user;

        $event = $this->eventRepository->create($data);

        return true;
    }

    public function range(array $data)
    {
        return $this->transformer->of(Event::class)->transform(
            $this->eventRepository->range(Carbon::parse($data['from']), Carbon::parse($data['to'])));
    }
}
<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Jobs\School\CreateEventLessons;
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
        if ($data['activityId'] == 0)
        {
            $data['activity'] = null;
        }
        else
        {
            $data['activity'] = $this->user->activities()->find($data['activityId']);
            if (is_null($data['activity']))
            {
                return false;
            }
        }

        $event = $this->eventRepository->create($data);

        if (sizeof($data['studentIds']) > 0)
        {
            $this->dispatchJob(new CreateEventLessons($event, $data['studentIds']));
        }

        return $event->getId();
    }

    public function range(array $data)
    {
        return $this->transformer->of(Event::class)->transform(
            $this->eventRepository->range(Carbon::parse($data['from']), Carbon::parse($data['to'])));
    }

    public function changeDate(array $data)
    {
        $event = $this->user->events()->find($data['id']);

        $startDate = Carbon::parse($event->startDate());
        $endDate = Carbon::parse($event->endDate());
        $newStartDate = Carbon::parse($data['newStartDate']);
        $newEndDate = $endDate->addDays($startDate->diffInDays($newStartDate));

        $event->setStartDate($newStartDate);
        $event->setEndDate($newEndDate);

        $this->eventRepository->update($event);

        return $newEndDate->toDateString();
    }

    public function upcomingEvents()
    {
        $events = $this->eventRepository->upcomingEvents();

        $grouped = [];
        foreach ($events as $event)
        {
            if (sizeof($grouped) == 4)
            {
                array_pop($grouped);
                break;
            }

            $grouped[Carbon::parse($event->startDate())->toDateString()][] =
                $this->transformer->of(Event::class)->transform($event);
        }

        return $grouped;
    }

    public function destroy($id)
    {
        // MAKE THIS ASYNC?

        $this->eventRepository->delete($this->user->events()->find($id));
    }
}
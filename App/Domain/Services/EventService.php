<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Jobs\School\CreateEventLessons;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class EventService extends AuthenticatedService
{
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
        $data['startDate'] = Carbon::parse($data['startDate'])->toDateString();
        $data['endDate'] = Carbon::parse($data['endDate'])->toDateString();
        $data['rEndDate'] = Carbon::parse($data['rEndDate'])->toDateString();

        $data['absoluteStart'] = $data['startDate'];
        $data['absoluteEnd'] = $data['endDate'];
        if ($data['isRecurring'])
        {
            $data['rEndsNever'] ? $data['absoluteEnd'] = Carbon::now()->addYears(100) : $data['rEndDate'];
        }

        $event = $this->repository->of(Event::class)->create($data);

        if (sizeof($data['studentIds']) > 0)
        {
            $this->dispatchJob(new CreateEventLessons($event, $data['studentIds']));
        }

        return $event->getId();
    }

    public function range(array $data)
    {
        $from = Carbon::parse($data['from'])->toDateString();
        $to = Carbon::parse($data['to'])->toDateString();

        $events = $this->user->events()->where('absoluteEnd', '>', $from)
            ->where('absoluteStart', '<', $to)
            ->toArray();

        return $this->transformer->of(Event::class)->transform($events);
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

        $this->repository->of(Event::class)->update($event);

        return $newEndDate->toDateString();
    }

    public function upcomingEvents()
    {
        $events = $this->repository->of(Event::class)->upcomingEventsOf($this->user);

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

        $this->repository->of(Event::class)->delete($this->user->events()->find($id));
    }
}
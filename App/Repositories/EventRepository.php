<?php

namespace App\Repositories;

use App\Domain\Events\Event;
use Carbon\Carbon;

class EventRepository extends AuthenticatedRepository
{
    public function create(array $data)
    {
        $event = new Event($data['title'], $data['description'], Carbon::parse($data['startDate']), Carbon::parse($data['endDate']),
            $data['startTime'], $data['endTime'], $data['isAllDay'], $data['color'], $data['teacher'], $data['activity']);

        $this->dm->persist($event);

        $this->dm->flush();

        return $event;
    }

    public function createLessons(array $lessons)
    {
        foreach ($lessons as $lesson)
        {
            $this->dm->persist($lesson);
        }

        $this->dm->flush();
    }

    public function range(Carbon $from, Carbon $to)
    {
        return $this->user->events()->where('endDate', '>', $from)
            ->where('startDate', '<', $to)
            ->toArray();
    }

    public function upcomingEvents()
    {
        return $this->user->events()->where('startDate', '>', Carbon::now())
            ->where('startDate', '<=', Carbon::now()->addWeek(1))
            ->sortBy('startDate', true)
            ->slice(0, 10);
    }

    public function update(Event $event)
    {
        $this->dm->flush();
    }

    public function delete(Event $event)
    {
        $this->dm->delete($event);

        $this->dm->flush();
    }
}
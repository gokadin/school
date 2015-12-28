<?php

namespace App\Repositories;

use App\Domain\Events\Event;
use Carbon\Carbon;

class EventRepository extends AuthenticatedRepository
{
    public function create(array $data)
    {
        $event = new Event($data['title'], Carbon::parse($data['startDate']), Carbon::parse($data['endDate']),
            $data['color'], $data['teacher']);

        $this->dm->persist($event);

        $this->dm->flush();

        return $event;
    }

    public function range(Carbon $from, Carbon $to)
    {
        return $this->user->events()->where('endDate', '>', $from)
            ->where('startDate', '<', $to)
            ->toArray();
    }
}
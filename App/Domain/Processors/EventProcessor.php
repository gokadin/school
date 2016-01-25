<?php

namespace App\Domain\Processors;

use App\Domain\Events\Event;
use Carbon\Carbon;

class EventProcessor
{
    public function displaceDates(Event $event, Carbon $newDate)
    {
        $startDate = Carbon::parse($event->startDate());
        $endDate = Carbon::parse($event->endDate());
        $dayDiff = $startDate->diffInDays($newDate);
        $newEndDate = $endDate->addDays($dayDiff);
        $absoluteEnd = Carbon::parse($event->absoluteEnd());
        $newAbsoluteEnd = $absoluteEnd->addDays($dayDiff);

        $event->setStartDate($newDate);
        $event->setEndDate($newEndDate);
        $event->setAbsoluteStart($newDate);
        $event->setAbsoluteEnd($newAbsoluteEnd);

        return $event;
    }

    public function copy(Event $event)
    {
        return new Event(
            $event->title(), $event->description(), $event->startDate(), $event->endDate(), $event->startTime(),
            $event->endTime(), $event->isAllDay(), $event->color(), $event->teacher(), $event->activity(),
            $event->isRecurring(), $event->rRepeat(), $event->rEvery(), $event->rEndDate(), $event->rEndsNever(),
            $event->location(), $event->visibility(), $event->notifyMeBy(), $event->notifyMeBefore(),
            $event->absoluteStart(), $event->absoluteEnd()
        );
    }
}
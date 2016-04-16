<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Processors\EventProcessor;
use App\Domain\Users\User;
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

        return $this->readAndTransform([$event], Carbon::parse($data['from']), Carbon::parse($data['to']));
    }

    public function range(User $user, Carbon $from, Carbon $to)
    {
        $events = $user->events()->where('absoluteEnd', '>', $from->toDateString())
            ->where('absoluteStart', '<', $to->toDateString())
            ->toArray();

        return $this->readAndTransform($events, $from, $to);
    }

    public function updateDate(int $id, Carbon $oldDate, Carbon $newDate): Event
    {
        $event = $this->user->events()->find($id);

        return $event->isRecurring()
            ? $this->updateRecurringDate($event, $oldDate, $newDate)
            : $this->updateNonRecurringDate($event, $newDate);
    }

    private function updateNonRecurringDate(Event $event, Carbon $newDate)
    {
        $eventProcessor = new EventProcessor();

        $event = $eventProcessor->displaceDates($event, $newDate);

        $this->repository->of(Event::class)->update($event);

        return $event;
    }

    private function updateRecurringDate(Event $event, Carbon $oldDate, Carbon $newDate)
    {
        $eventProcessor = new EventProcessor();

        $event->skip($oldDate);

        $this->repository->of(Event::class)->update($event);

        $newEvent = $eventProcessor->copy($event);
        $newEvent->setIsRecurring(false);
        $eventProcessor->displaceDates($newEvent, $newDate);

        $this->repository->of(Event::class)->create($event);

        // WHAT??? REFACTORING MESS... EVERYWHERE...

        if ($event->lessons()->count() > 0)
        {
            $studentIds = [];
            foreach ($event->lessons() as $lesson)
            {
                $studentIds[] = $lesson->student()->getId();
            }

            $this->dispatchJob(new CreateEventLessons($newEvent, $studentIds));
        }

        return $newEvent;
    }

    public function upcomingEvents()
    {
        $events = $this->readAndTransform($this->repository->of(Event::class)
            ->upcomingEventsOf($this->user->events()), Carbon::now(), Carbon::now()->addYears(10), 10);

        usort($events, function($a, $b) {
            return Carbon::parse($a['startDate'])->lt(Carbon::parse($b['startDate'])) ? -1 : 1;
        });

        return $events;
    }

    public function destroy($id)
    {
        // MAKE THIS ASYNC?

        $this->repository->of(Event::class)->delete($this->user->events()->find($id));
    }

    private function readAndTransform(array $events, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence = 99999)
    {
        $result = [];
        foreach ($events as $event)
        {
            $transformed = $this->transformer->of(Event::class)->transform($event);

            if (!$event->isRecurring())
            {
                $result[] = $transformed;

                continue;
            }

            $result = array_merge($result, $this->readRecurring($transformed, $minDate, $maxDate, $maxPerRecurrence, $event->skipDates()));
        }

        return $result;
    }

    private function readRecurring(array $transformed, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence, array $skipDates)
    {
        list($startDate, $endDate) = $this->initialRecurringDates($transformed, $minDate);

        $recurrence = $transformed;
        $recurrence['startDate'] = $startDate->toDateString();
        $recurrence['endDate'] = $endDate->toDateString();

        $result = [];
        if (!in_array($startDate->toDateString(), $skipDates))
        {
            $result[] = $recurrence;
        }

        for ($i = 0; $i < $maxPerRecurrence; $i++)
        {
            list($startDate, $endDate) = $this->nextRecurringDates($transformed['rRepeat'], $startDate, $endDate);

            if (in_array($startDate->toDateString(), $skipDates))
            {
                continue;
            }

            if ($startDate->gt($maxDate))
            {
                return $result;
            }

            $recurrence = $transformed;
            $recurrence['startDate'] = $startDate->toDateString();
            $recurrence['endDate'] = $endDate->toDateString();

            $result[] = $recurrence;
        }

        return $result;
    }

    private function initialRecurringDates(array $transformed, Carbon $minDate)
    {
        $startDate = Carbon::parse($transformed['startDate']);
        $endDate = Carbon::parse($transformed['endDate']);

        if ($minDate->lte($startDate))
        {
            return [$startDate, $endDate];
        }

        switch ($transformed['rRepeat'])
        {
            case 'daily':
                $diffInDays = $startDate->diffInDays($minDate);
                return [$startDate->addDays($diffInDays), $endDate->addDays($diffInDays)];
            case 'weekly':
                $diffInWeeks = $startDate->diffInWeeks($minDate);
                return [$startDate->addWeeks($diffInWeeks), $endDate->addWeeks($diffInWeeks)];
            case 'monthly':
                $diffInMonths = $startDate->diffInMonths($minDate);
                return [$startDate->addMonths($diffInMonths), $endDate->addMonths($diffInMonths)];
            case 'yearly':
                $diffInYears = $startDate->diffInYears($minDate);
                return [$startDate->addYears($diffInYears), $endDate->addYears($diffInYears)];
        }
    }

    private function nextRecurringDates($repeat, Carbon $startDate, Carbon $endDate)
    {
        switch ($repeat)
        {
            case 'daily':
                return [$startDate->addDay(), $endDate->addDay()];
            case 'weekly':
                return [$startDate->addWeek(), $endDate->addWeek()];
            case 'monthly':
                return [$startDate->addMonth(), $endDate->addMonth()];
            case 'yearly':
                return [$startDate->addYear(), $endDate->addYear()];
        }
    }
}
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
        $from = Carbon::parse($data['from']);
        $to = Carbon::parse($data['to']);

        $events = $this->user->events()->where('absoluteEnd', '>', $from->toDateString())
            ->where('absoluteStart', '<', $to->toDateString())
            ->toArray();

        return $this->readAndTransform($events, $from, $to);
    }

    public function changeDate(array $data)
    {
        $event = $this->user->events()->find($data['id']);

        return $event->isRecurring()
            ? $this->changeRecurringEventDate($event, Carbon::parse($data['oldDate']), Carbon::parse($data['newDate']))
            : $this->changeNonRecurringEventDate($event, Carbon::parse($data['newDate']));
    }

    private function changeNonRecurringEventDate(Event $event, Carbon $newDate)
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

        $this->repository->of(Event::class)->update($event);

        return [
            'newStartDate' => $newDate->toDateString(),
            'newEndDate' => $newEndDate->toDateString()
        ];
    }

    private function changeRecurringEventDate(Event $event, Carbon $oldDate, Carbon $newDate)
    {
        $event->skip($oldDate);

        $this->repository->of(Event::class)->update($event);

        $diffInDays = Carbon::parse($event->startDate())->diffInDays(Carbon::parse($event->endDate()));
        $newEvent = $this->repository->of(Event::class)->create([
            'title' => $event->title(), 'description' => $event->description(), 'startDate' => $newDate,
            'endDate' => $newDate->addDays($diffInDays), 'startTime' => $event->startTime(), 'endTime' => $event->endTime(),
            'isAllDay' => $event->isAllDay(), 'color' => $event->color(), 'teacher' => $event->teacher(), 'activity' => $event->activity(),
            'isRecurring' => false, 'rRepeat' => $event->rRepeat(), 'rEvery' => $event->rEvery(), 'rEndDate' => $event->rEndDate(),
            'rEndsNever' => $event->rEndsNever(), 'location' => $event->location(), 'visibility' => $event->visibility(),
            'notifyMeBy' => $event->notifyMeBy(), 'notifyMeBefore' => $event->notifyMeBefore(),
            'absoluteStart' => $event->absoluteStart(), 'absoluteEnd' => $event->absoluteEnd()
        ]);

        if ($event->lessons()->count() > 0)
        {
            $studentIds = [];
            foreach ($event->lessons() as $lesson)
            {
                $studentIds[] = $lesson->student()->getId();
            }

            $this->dispatchJob(new CreateEventLessons($newEvent, $studentIds));
        }

        return [
            'newEvent' => $this->transformer->of(Event::class)->transform($newEvent)
        ];
    }

    public function upcomingEvents()
    {
        $events = $this->readAndTransform($this->repository->of(Event::class)
            ->upcomingEventsOf($this->user->events()), Carbon::now(), Carbon::now()->addYears(10), 10);

        $grouped = [];
        foreach ($events as $event)
        {
            if (sizeof($grouped) == 4)
            {
                array_pop($grouped);
                break;
            }

            $grouped[$event['startDate']][] = $event;
        }

        return $grouped;
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
            case 'weekly':
                $diffInWeeks = $startDate->diffInWeeks($minDate);
                return [$startDate->addWeeks($diffInWeeks), $endDate->addWeeks($diffInWeeks)];
        }
    }

    private function nextRecurringDates($repeat, Carbon $startDate, Carbon $endDate)
    {
        switch ($repeat)
        {
            case 'weekly':
                return [$startDate->addWeek(), $endDate->addWeek()];
        }
    }
}
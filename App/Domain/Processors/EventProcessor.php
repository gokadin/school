<?php

namespace App\Domain\Processors;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
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

    public function copyLesson(Lesson $lesson): Lesson
    {
        return new Lesson($lesson->event(), $lesson->student(), $lesson->absoluteStart(), $lesson->absoluteEnd());
    }

    public function extractLessons(array $lessons, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence = 99999)
    {
        $result = [];
        foreach ($lessons as $lesson)
        {
            $event = $lesson->event();

            if (!$event->isRecurring())
            {
                $result[] = $lesson;//$transformed;

                continue;
            }

            $result = array_merge($result, $this->readRecurringLesson($lesson, $event, $lesson->missedDates(), $minDate, $maxDate, $maxPerRecurrence));
        }

        return $result;
    }

    private function readRecurringLesson(Lesson $lesson, Event $event, array $missedDates, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence)
    {
        list($startDate, $endDate) = $this->initialRecurringDates($event->startDate(), $event->endDate(), $event->rRepeat(), $minDate);

        $recurrence = $this->copyLesson($lesson);
        $recurrence->event()->setStartDate($startDate->toDateString());
        $recurrence->event()->setEndDate($endDate->toDateString());

        $result = [];
        if (!in_array($startDate->toDateString(), $event->skipDates()))
        {
            $result[] = $recurrence;
        }

        for ($i = 0; $i < $maxPerRecurrence; $i++)
        {
            list($startDate, $endDate) = $this->nextRecurringDates($event->rRepeat(), $startDate, $endDate);

            if (in_array($startDate->toDateString(), $event->skipDates()))
            {
                continue;
            }

            if ($startDate->gt($maxDate))
            {
                return $result;
            }

            $recurrence = $this->copyLesson($lesson);
            $recurrence->event()->setStartDate($startDate->toDateString());
            $recurrence->event()->setEndDate($endDate->toDateString());

            $result[] = $recurrence;
        }

        return $result;
    }

    private function initialRecurringDates($startDate, $endDate, $rRepeat, Carbon $minDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($minDate->lte($startDate))
        {
            return [$startDate, $endDate];
        }

        switch ($rRepeat)
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
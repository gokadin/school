<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
use App\Domain\Users\Student;
use Carbon\Carbon;

class LessonService extends AuthenticatedService
{
    public function upcoming($id, $data)
    {
        $student = $this->repository->of(Student::class)->find($id);

        $from = Carbon::now();
        $to = Carbon::parse($data['to']);
        $lessons = $student->lessons()->where('absoluteEnd', '>', $from->toDateString())
            ->where('absoluteStart', '<', $to->toDateString())
            ->toArray();;

        $lessons = $this->readLessons($lessons, $from, $to);

        $grouped = [];
        foreach ($lessons as $lesson)
        {
            $grouped[$lesson['startDate']][] = $lesson;
        }

        return [
            'lessons' => $grouped
        ];
    }

    public function updateAttendance(array $data)
    {
        $lesson = $this->user->events()->find($data['eventId'])->lessons()->find($data['lessonId']);

        $data['attended']
            ? $lesson->attend(Carbon::parse($data['date']))
            : $lesson->miss(Carbon::parse($data['date']));

        $this->repository->of(Event::class)->updateLesson($lesson);
    }

    public function readLessons(array $lessons, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence = 99999)
    {
        $result = [];
        foreach ($lessons as $lesson)
        {
            $event = $lesson->event();
            $transformed = $this->transformer->of(Lesson::class)->transform($lesson);

            if (!$event->isRecurring())
            {
                $result[] = $transformed;

                continue;
            }

            $result = array_merge($result, $this->readRecurringLesson($transformed, $event, $lesson->missedDates(), $minDate, $maxDate, $maxPerRecurrence));
        }

        return $result;
    }

    private function readRecurringLesson(array $transformed, Event $event, array $missedDates, Carbon $minDate, Carbon $maxDate, $maxPerRecurrence)
    {
        list($startDate, $endDate) = $this->initialRecurringDates($transformed['startDate'], $transformed['endDate'], $event->rRepeat(), $minDate);

        $recurrence = $transformed;
        $recurrence['startDate'] = $startDate->toDateString();
        $recurrence['endDate'] = $endDate->toDateString();
        $recurrence['attended'] = !isset($missedDates[$recurrence['startDate']]);

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

            $recurrence = $transformed;
            $recurrence['startDate'] = $startDate->toDateString();
            $recurrence['endDate'] = $endDate->toDateString();
            $recurrence['attended'] = !isset($missedDates[$recurrence['startDate']]);

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
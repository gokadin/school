<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
use App\Domain\Users\Student;
use App\Domain\Users\TempStudent;
use App\Events\School\StudentPreRegistered;
use Carbon\Carbon;

class StudentService extends AuthenticatedService
{
    public function findStudent($id)
    {
        return $this->user->students()->find($id);
    }

    public function getStudentList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        $data = $this->repository->paginate($this->user->students(),
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);

        return [
            'students' => $this->transformer->of(Student::class)->transform($data['data']),
            'pagination' => $data['pagination']
        ];
    }

    public function getInIds(array $ids)
    {
        return $this->transformer->of(Student::class)->transform(
            $this->repository->of(Student::class)->findIn($ids)->toArray());
    }

    public function search($data)
    {
        return $this->transformer->of(Student::class)->transform(
            $this->repository->of(Student::class)->search($data['search'], $this->user->students()));
    }

    public function preRegister(array $data)
    {
        $activity = $this->user->activities()->find($data['activityId']);

        if (is_null($activity))
        {
            return false;
        }

        $data['teacher'] = $this->user;
        $data['activity'] = $activity;

        $tempStudent = $this->repository->of(Student::class)->preRegister($data);

        if (is_null($tempStudent))
        {
            return false;
        }

        $this->fireEvent(new StudentPreRegistered($tempStudent));

        return true;
    }

    public function getProfile($id)
    {
        $student = $this->repository->of(Student::class)->find($id);

        return [
            'student' => $student,
            'registrationForm' => json_decode($student->teacher()->settings()->registrationForm(), true)
        ];
    }

    public function newStudents()
    {
        return $this->transformer->of(TempStudent::class)
            ->transform($this->repository->of(Student::class)->newStudentsOf($this->user)->toArray());
    }

    public function getLessons($id, $data)
    {
        $student = $this->repository->of(Student::class)->find($id);

        $from = Carbon::parse($data['from']);
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
            $found = false;
            foreach ($missedDates as $missedDate)
            {
                if ($missedDate == $recurrence['startDate'])
                {
                    $recurrence['attended'] = false;
                    $found = true;
                    break;
                }
            }
            if (!$found)
            {
                $recurrence['attended'] = true;
            }

            $result[] = $recurrence;
        }

        return $result;
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
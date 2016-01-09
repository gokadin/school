<?php

namespace App\Repositories;

use App\Domain\Events\Event;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class EventRepository extends RepositoryBase
{
    public function create(array $data)
    {
        $event = new Event($data['title'], $data['description'], $data['startDate'],
            $data['endDate'], $data['startTime'], $data['endTime'], $data['isAllDay'],
            $data['color'], $data['teacher'], $data['activity'], $data['isRecurring'], $data['rRepeat'],
            $data['rEvery'], $data['rEndDate'], $data['rEndsNever'], $data['location'], $data['visibility'],
            $data['notifyMeBy'], $data['notifyMeBefore'], $data['absoluteStart'], $data['absoluteEnd']);

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

    public function upcomingEventsOf(Teacher $teacher)
    {
        return $teacher->events()->where('startDate', '>', Carbon::now())
            ->where('startDate', '<=', Carbon::now()->addWeek(1))
            ->sortBy('startDate', true)
            ->slice(0, 10);
    }
}
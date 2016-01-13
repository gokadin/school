<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use Carbon\Carbon;

class LessonService extends AuthenticatedService
{
    public function updateAttendance(array $data)
    {
        $lesson = $this->user->events()->find($data['eventId'])->lessons()->find($data['lessonId']);

        $data['attended']
            ? $lesson->attend(Carbon::parse($data['date']))
            : $lesson->miss(Carbon::parse($data['date']));

        $this->repository->of(Event::class)->updateLesson($lesson);
    }
}
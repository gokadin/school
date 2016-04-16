<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Users\Student;
use Carbon\Carbon;

class LessonService extends AuthenticatedService
{
    public function getLessons(Student $student, Carbon $from, Carbon $to)
    {
//        $lessons = $student->lessons()->where('absoluteEnd', '>', $from->toDateString())
//            ->where('absoluteStart', '<', $to->toDateString())
//            ->toArray();

//        $lessons = $this->eventProcessor->extractLessons($lessons, $from, $to);
//
//        usort($lessons, function($a, $b) {
//            return Carbon::parse($a->event()->startDate())->lt(Carbon::parse($b->event()->startDate())) ? -1 : 1;
//        });

        return $student->lessons()->toArray();
    }

    public function updateAttendance(array $data)
    {
        $lesson = $this->user->events()->find($data['eventId'])->lessons()->find($data['lessonId']);

        $data['attended']
            ? $lesson->attend(Carbon::parse($data['date']))
            : $lesson->miss(Carbon::parse($data['date']));

        $this->repository->of(Event::class)->updateLesson($lesson);
    }
}
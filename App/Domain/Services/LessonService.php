<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
use App\Domain\Processors\EventProcessor;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Repositories\Repository;
use Carbon\Carbon;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class LessonService extends AuthenticatedService
{
    /**
     * @var EventProcessor
     */
    private $eventProcessor;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                Repository $repository, Authenticator $authenticator, EventProcessor $eventProcessor)
    {
        parent::__construct($queue, $eventManager, $transformer, $repository, $authenticator);

        $this->eventProcessor = $eventProcessor;
    }

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
<?php

namespace App\Jobs\School;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
use App\Jobs\Job;
use App\Repositories\EventRepository;
use App\Repositories\UserRepository;
use Library\Queue\JobFailedException;

class CreateEventLessons extends Job
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var array
     */
    private $studentIds;

    public function __construct(Event $event, array $studentIds)
    {
        $this->event = $event;
        $this->studentIds = $studentIds;
    }

    public function handle(EventRepository $eventRepository, UserRepository $userRepository)
    {
        $lessons = [];
        foreach ($this->studentIds as $id)
        {
            $student = $userRepository->findStudent($id);
            if (is_null($student))
            {
                throw new JobFailedException('CreateEventLessons.handle : Could not find student with id '.$id.'.');
            }

            $lessons[] = new Lesson($this->event, $student);
        }

        $eventRepository->createLessons($lessons);
    }
}
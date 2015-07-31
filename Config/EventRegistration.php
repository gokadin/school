<?php

namespace Config;

use App\Events\Frontend\TeacherPreRegistered;
use App\Listeners\Frontend\EmailPreRegisteredTeacher;

class EventRegistration
{
    protected $eventManager;

    public function __construct($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function registerEvents()
    {
//        $this->eventManager->register(TeacherPreRegistered::class, [
//            EmailPreRegisteredTeacher::class
//        ]);
    }
}
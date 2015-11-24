<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherRegistered;
use App\Events\Frontend\UserLoggedIn;
use App\Listeners\Listener;
use App\Repositories\UserRepository;
use Library\Events\EventManager;

class LoginTeacher extends Listener
{
    protected $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function handle(TeacherRegistered $event, UserRepository $userRepository)
    {
        $userRepository->loginTeacher($event->teacher());

        $this->eventManager->fire(new UserLoggedIn($event->teacher(), 'teacher'));
    }
}
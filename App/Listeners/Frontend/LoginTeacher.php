<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherRegistered;
use App\Listeners\Listener;
use App\Repositories\UserRepository;

class LoginTeacher extends Listener
{
    public function handle(TeacherRegistered $event, UserRepository $userRepository)
    {
        $userRepository->loginTeacher($event->teacher());
    }
}
<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherRegistered;
use App\Listeners\Listener;
use Library\Queue\ShouldQueue;

class SendRegistrationEmail extends Listener implements ShouldQueue
{
    public function handle(TeacherRegistered $event)
    {

    }
}
<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Listeners\Listener;
use Library\Queue\ShouldQueue;

class SendPreRegistrationEmail extends Listener implements ShouldQueue
{
    public function handle(TeacherPreRegistered $event)
    {

    }
}
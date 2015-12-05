<?php

namespace App\Listeners\School;

use App\Events\School\StudentPreRegistered;
use App\Listeners\Listener;
use Library\Queue\ShouldQueue;

class SendStudentPreRegistrationEmail extends Listener implements ShouldQueue
{
    public function handle(StudentPreRegistered $event)
    {

    }
}
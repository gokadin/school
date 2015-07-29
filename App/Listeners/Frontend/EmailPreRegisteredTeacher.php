<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Listeners\Listener;
use App\Repositories\Contracts\IUserRepository;
use Library\Facades\Log;
use Library\Queue\ShouldQueue;

class EmailPreRegisteredTeacher extends Listener implements ShouldQueue
{
    protected $r;

    public function __construct(IUserRepository $r)
    {
        $this->r = $r;
    }

    public function handle(TeacherPreRegistered $event)
    {
        Log::info('Fired! Teacher name -> '.$event->teacher()->name().
            ' Also resolved repo -> '.get_class($this->r));
    }
}
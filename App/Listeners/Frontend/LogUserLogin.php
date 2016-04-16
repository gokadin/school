<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\UserLoggedIn;
use Library\Events\Listener;
use Library\Events\ShouldQueue;
use Library\Log\Log;

class LogUserLogin extends Listener implements ShouldQueue
{
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function handle(UserLoggedIn $event)
    {
        $this->log->info(ucfirst($event->type()).' '.$event->user()->name().' logged in.');
    }
}
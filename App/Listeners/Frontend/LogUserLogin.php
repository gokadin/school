<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\UserLoggedIn;
use App\Listeners\Listener;
use Library\Queue\ShouldQueue;

class LogUserLogin extends Listener implements ShouldQueue
{
    public function handle(UserLoggedIn $event)
    {
        // ...

        $user = $event->user();
        $type = $event->type();

        // no need for userrepo.. need stats stuff
    }
}
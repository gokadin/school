<?php

namespace Library\Events;

use Library\Facades\App;

trait FiresEvents
{
    public function fireEvent($event)
    {
        $eventManager = App::container()->resolveInstance('eventManager');

        $eventManager->fire($event);
    }
}
<?php

namespace App\Jobs;

use Library\Events\FiresEvents;
use Library\Queue\Queueable;

abstract class Job
{
    use Queueable, FiresEvents;
}
<?php

namespace App\Jobs;

use Library\Queue\Queueable;

abstract class Job
{
    use Queueable;
}
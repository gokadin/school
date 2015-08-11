<?php

namespace App\Listeners;

use Library\Queue\Queueable;

abstract class Listener
{
    use Queueable;
}
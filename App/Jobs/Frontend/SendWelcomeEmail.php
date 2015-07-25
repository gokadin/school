<?php

namespace App\Jobs\Frontend;

use App\Jobs\Job;
use Library\Facades\Log;

class SendWelcomeEmail extends Job
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Log::info('Executed queue job! : temp user -> '.$this->user->email);
    }
}
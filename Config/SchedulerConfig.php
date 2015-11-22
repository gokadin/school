<?php

namespace Config;

use App\Repositories\UserRepository;
use Library\Application;
use Library\Scheduler\Scheduler;

class SchedulerConfig
{
    protected $app;

    public function __construct()
    {
        $this->app = new Application();
    }

    public function run(Scheduler $scheduler)
    {
        $scheduler->add('remove expired temp teachers', function() {
            $userRepository = new UserRepository();
            $userRepository->removeExpiredTempTeachers();
        })->dailyAt('3:00');
    }
}
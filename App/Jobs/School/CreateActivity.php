<?php

namespace App\Jobs\School;

use App\Jobs\Job;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;

class CreateActivity extends Job
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(ActivityRepository $activityRepository, UserRepository $userRepository)
    {
        $this->data['teacher'] = $userRepository->getLoggedInUser();

        $activityRepository->create($this->data);
    }
}
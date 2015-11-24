<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
use Library\Queue\JobFailedException;
use Library\Queue\ShouldQueue;
use App\Repositories\UserRepository;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(UserRepository $userRepository)
    {
        $tempTeacher = $userRepository->preRegisterTeacher($this->data);

        if (!$tempTeacher)
        {
            throw new JobFailedException('Could not pre-register teacher.');
            return;
        }

        $this->eventManager->fire(new TeacherPreRegistered($tempTeacher));
    }
}
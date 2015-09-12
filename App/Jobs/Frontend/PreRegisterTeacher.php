<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
use Library\Events\FiresEvents;
use Library\Queue\JobFailedException;
use Library\Queue\ShouldQueue;
use App\Repositories\UserRepository;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    use FiresEvents;

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

        $this->fireEvent(new TeacherPreRegistered($tempTeacher));
    }
}
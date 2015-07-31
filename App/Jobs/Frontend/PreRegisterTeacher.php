<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
use App\Repositories\Contracts\IUserRepository;
use Library\Events\FiresEvents;
use Library\Queue\JobFailedException;
use Library\Queue\ShouldQueue;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    use FiresEvents;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(IUserRepository $userRepository)
    {
        $tempTeacher = $userRepository->preRegisterTeacher($this->data);

        if (!$tempTeacher)
        {
            throw new JobFailedException('Could not register temp teacher. Repository returned false.');
            return;
        }

        $this->fireEvent(new TeacherPreRegistered($tempTeacher));
    }
}
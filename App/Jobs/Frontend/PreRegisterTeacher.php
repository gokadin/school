<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
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

        $this->fireEvent(new TeacherPreRegistered($tempTeacher));
    }
}
<?php

namespace App\Jobs\Frontend;

use App\Domain\Users\Teacher;
use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
use App\Repositories\Repository;
use Library\Queue\ShouldQueue;
use App\Repositories\UserRepository;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(Repository $repository)
    {
        $tempTeacher = $repository->of(Teacher::class)->preRegister($this->data);

        $this->fireEvent(new TeacherPreRegistered($tempTeacher));
    }
}
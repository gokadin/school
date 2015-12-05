<?php

namespace App\Jobs\School;

use App\Events\School\StudentPreRegistered;
use App\Jobs\Job;
use App\Repositories\UserRepository;

class PreRegisterStudent extends Job
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(UserRepository $userRepository)
    {
        $teacher = $userRepository->getLoggedInUser();
        $activity = $teacher->activities()->find($this->data['activityId']);

        $tempStudent = $userRepository->preRegisterStudent($teacher, $activity, $this->data);

        $this->fireEvent(new StudentPreRegistered($tempStudent));
    }
}
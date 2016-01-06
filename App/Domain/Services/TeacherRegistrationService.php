<?php

namespace App\Domain\Services;

use App\Jobs\Frontend\PreRegisterTeacher;

class TeacherRegistrationService extends Service
{
    public function preRegister(array $data)
    {
        $this->dispatchJob(new PreRegisterTeacher($data));
    }
}
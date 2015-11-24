<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherRegistered;
use App\Jobs\Job;
use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Session\Session;

class RegisterTeacher extends Job
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(UserRepository $userRepository, Response $response, Session $session)
    {
        $teacher = $userRepository->registerTeacher($this->data);
        if (!$teacher)
        {
            $session->setFlash('Your account no longer exists. Please try signing up again.');
            $response->route('frontend.account.signUp');
        }

        $this->fireEvent(new TeacherRegistered($teacher));
    }
}
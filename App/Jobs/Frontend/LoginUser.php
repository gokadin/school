<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\StudentLoggedIn;
use App\Events\Frontend\TeacherLoggedIn;
use App\Jobs\Job;
use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Session\Session;

class LoginTeacher extends Job
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(UserRepository $userRepository, Session $session, Response $response)
    {
        $teacher = $userRepository->attemptLogin(Teacher::class, $this->data['email'], md5($this->data['password']));

        if ($teacher != false)
        {
            $this->fireEvent(new TeacherLoggedIn($teacher));
            $response->route('school.teacher.index.index');
            return;
        }

        $student = $userRepository->attemptLogin(Student::class, $this->data['email'], md5($this->data['password']));

        if ($student != false)
        {
            $this->fireEvent(new StudentLoggedIn($student));
            $response->route('school.student.index.index');
            return;
        }

        $session->setFlash('The email or password is incorrect. Please try again.');
        $response->back();
    }
}
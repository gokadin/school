<?php

namespace App\Jobs\Frontend;

use App\Domain\Users\Teacher;
use App\Domain\Users\Student;
use App\Events\Frontend\UserLoggedIn;
use App\Jobs\Job;
use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Session\Session;

class LoginUser extends Job
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
            $this->fireEvent(new UserLoggedIn($teacher, 'teacher'));
            $response->route('school.teacher.index.index');
            return;
        }

        $student = $userRepository->attemptLogin(Student::class, $this->data['email'], md5($this->data['password']));

        if ($student != false)
        {
            $this->fireEvent(new UserLoggedIn($student, 'student'));
            $response->route('school.student.index.index');
            return;
        }

        $session->setFlash('The email or password is incorrect. Please try again.');
        $response->route('frontend.account.index');
    }
}
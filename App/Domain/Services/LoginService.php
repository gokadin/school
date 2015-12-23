<?php

namespace App\Domain\Services;

use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use App\Events\Frontend\UserLoggedIn;

class LoginService extends AuthenticatedService
{
    public function login(array $data)
    {
        $teacher = $this->userRepository->attemptLogin(Teacher::class, $data['email'], md5($data['password']));

        if ($teacher != false)
        {
            $this->fireEvent(new UserLoggedIn($teacher, 'teacher'));

            return true;
        }

        $student = $this->userRepository->attemptLogin(Student::class, $data['email'], md5($data['password']));

        if ($student != false)
        {
            $this->fireEvent(new UserLoggedIn($student, 'student'));

            return true;
        }

        return false;
    }

    /**
     * @return \App\Domain\Users\User
     */
    public function user()
    {
        return $this->userRepository->getLoggedInUser();
    }
}
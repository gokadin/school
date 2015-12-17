<?php

namespace App\Domain\Services;

use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use App\Events\Frontend\UserLoggedIn;
use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;

class LoginService extends Service
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Queue $queue, EventManager $eventManager, UserRepository $userRepository)
    {
        parent::__construct($queue, $eventManager);

        $this->userRepository = $userRepository;
    }

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
}
<?php

namespace App\Domain\Services;

use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use App\Events\Frontend\UserLoggedIn;
use App\Repositories\Repository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class LoginService extends Service
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                Repository $repository, Authenticator $authenticator)
    {
        parent::__construct($queue, $eventManager, $transformer, $repository);

        $this->authenticator = $authenticator;
    }

    public function login(array $data)
    {
        $teacher = $this->authenticator->attemptLogin(Teacher::class, $data['email'], md5($data['password']));

        if ($teacher != false)
        {
            $this->fireEvent(new UserLoggedIn($teacher, 'teacher'));

            return 'teacher';
        }

        $student = $this->authenticator->attemptLogin(Student::class, $data['email'], md5($data['password']));

        if ($student != false)
        {
            $this->fireEvent(new UserLoggedIn($student, 'student'));

            return 'student';
        }

        return false;
    }

    public function logout()
    {
        $this->authenticator->logout();
    }
}
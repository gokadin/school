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

    public function __construct(EventManager $eventManager, Repository $repository, Authenticator $authenticator)
    {
        parent::__construct($eventManager, $repository);

        $this->authenticator = $authenticator;
    }

    public function login($email, $password)
    {
        $password = md5($password);

        $data = $this->authenticator->attemptLogin(Teacher::class, $email, $password);

        if ($data != false)
        {
            $this->fireEvent(new UserLoggedIn($data['user'], 'teacher'));

            return $data['authToken'];
        }

        $data = $this->authenticator->attemptLogin(Student::class, $email, $password);

        if ($data != false)
        {
            $this->fireEvent(new UserLoggedIn($data['user'], 'student'));

            return $data['authToken'];
        }

        return false;
    }

    public function logout()
    {
        $this->authenticator->logout();
    }
}
<?php

namespace App\Domain\Services;

use App\Domain\Users\Authenticator;
use App\Domain\Users\Teacher;
use App\Events\Frontend\TeacherRegistered;
use App\Events\Frontend\UserLoggedIn;
use App\Repositories\Repository;
use Library\Events\EventManager;

class TeacherRegistrationService extends Service
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

    public function preRegister(array $data)
    {
        $this->dispatchJob(new PreRegisterTeacher($data));
    }

    public function findTempTeacher($id, $code)
    {
        $tempTeacher = $this->repository->of(Teacher::class)->findTempTeacher($id);

        if (is_null($tempTeacher) || $tempTeacher->confirmationCode() != $code)
        {
            return false;
        }

        return $tempTeacher;
    }

    public function register(array $data)
    {
        $teacher = $this->repository->of(Teacher::class)->create($data);
        if (is_null($teacher))
        {
            return false;
        }

        $this->authenticator->loginTeacher($teacher);

        $this->fireEvent(new TeacherRegistered($teacher));
        $this->fireEvent(new UserLoggedIn($teacher, 'teacher'));

        return true;
    }
}
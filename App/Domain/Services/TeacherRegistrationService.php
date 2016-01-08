<?php

namespace App\Domain\Services;

use App\Events\Frontend\TeacherRegistered;
use App\Events\Frontend\UserLoggedIn;
use App\Jobs\Frontend\PreRegisterTeacher;
use App\Jobs\Frontend\RegisterTeacher;
use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class TeacherRegistrationService extends Service
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                UserRepository $userRepository)
    {
        parent::__construct($queue, $eventManager, $transformer);

        $this->userRepository = $userRepository;
    }

    public function preRegister(array $data)
    {
        $this->dispatchJob(new PreRegisterTeacher($data));
    }

    public function findTempTeacher($id, $code)
    {
        $tempTeacher = $this->userRepository->findTempTeacher($id);

        if (is_null($tempTeacher) || $tempTeacher->confirmationCode() != $code)
        {
            return false;
        }

        return $tempTeacher;
    }

    public function register(array $data)
    {
        $teacher = $this->userRepository->registerTeacher($data);
        if (is_null($teacher))
        {
            return false;
        }

        $this->userRepository->loginTeacher($teacher);

        $this->fireEvent(new TeacherRegistered($teacher));
        $this->fireEvent(new UserLoggedIn($teacher, 'teacher'));

        return true;
    }
}
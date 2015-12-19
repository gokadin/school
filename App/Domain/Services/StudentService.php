<?php

namespace App\Domain\Services;

use App\Events\School\StudentPreRegistered;
use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;

class StudentService extends Service
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

    public function getStudentList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        return $this->userRepository->paginate(
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);
    }

    public function preRegister(array $data)
    {
        $teacher = $this->userRepository->getLoggedInUser();
        $activity = $teacher->activities()->find($data['activityId']);

        if (is_null($activity))
        {
            return false;
        }

        $tempStudent = $this->userRepository->preRegisterStudent($teacher, $activity, $data);

        if (is_null($tempStudent))
        {
            return false;
        }

        $this->fireEvent(new StudentPreRegistered($tempStudent));

        return true;
    }

    public function getProfile($id)
    {
        $student = $this->userRepository->findStudent($id);

        return [
            'student' => $student,
            'registrationForm' => json_decode($student->teacher()->settings()->registrationForm(), true)
        ];
    }
}
<?php

namespace App\Domain\Services;

use App\Domain\Processors\RegistrationFormProcessor;
use App\Domain\Users\Authenticator;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use App\Events\School\StudentPreRegistered;
use App\Repositories\Repository;
use Carbon\Carbon;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class StudentService extends AuthenticatedService
{
    /**
     * @var RegistrationFormProcessor
     */
    private $registrationFormProcessor;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer, Repository $repository,
                                Authenticator $authenticator, RegistrationFormProcessor $registrationFormProcessor)
    {
        parent::__construct($queue, $eventManager, $transformer, $repository, $authenticator);

        $this->registrationFormProcessor = $registrationFormProcessor;
    }

    public function findStudent($id)
    {
        return $this->user->students()->find($id);
    }

    public function getStudentList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        $data = $this->repository->paginate($this->user->students(),
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);

        return [
            'students' => $this->transformer->of(Student::class)->transform($data['data']),
            'pagination' => $data['pagination']
        ];
    }

    public function paginate(Teacher $teacher, int $page, int $max, array $sortingRules, array $searchRules): array
    {
        return $this->repository->paginate(
            $teacher->students(), $page, $max > 20 ? 20 : $max, $sortingRules, $searchRules);
    }

    public function pending(Teacher $teacher)
    {
        return $this->repository->of(Student::class)->pendingStudentsOfTeacher($teacher)->toArray();
    }

    public function getInIds(array $ids)
    {
        return $this->transformer->of(Student::class)->transform(
            $this->repository->of(Student::class)->findIn($ids)->toArray());
    }

    public function search($data)
    {
        return $this->transformer->of(Student::class)->transform(
            $this->repository->of(Student::class)->search($data['search'], $this->user->students()));
    }

    public function preRegister(array $data)
    {
        $activity = $this->user->activities()->find($data['activityId']);

        if (is_null($activity))
        {
            return false;
        }

        $data['teacher'] = $this->user;
        $data['activity'] = $activity;

        $tempStudent = $this->repository->of(Student::class)->preRegister($data);

        if (is_null($tempStudent))
        {
            return false;
        }

        $this->fireEvent(new StudentPreRegistered($tempStudent));

        return true;
    }

    public function getProfile($id): array
    {
        $student = $this->repository->of(Student::class)->find($id);

        return [
            'student' => $student,
            'profileInformation' => $this->registrationFormProcessor->buildProfileInformation($student)
        ];
    }
}
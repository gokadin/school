<?php

namespace App\Domain\Services;

use App\Domain\Users\Student;
use App\Domain\Users\TempStudent;
use App\Events\School\StudentPreRegistered;

class StudentService extends AuthenticatedService
{
    public function getStudentList(array $data)
    {
        $sortingRules = isset($data['sortingRules']) ? $data['sortingRules'] : [];
        $searchRules = isset($data['searchRules']) ? $data['searchRules'] : [];

        return $this->userRepository->paginate(
            $data['page'], $data['max'] > 20 ? 20 : $data['max'], $sortingRules, $searchRules);
    }

    public function getInIds(array $ids)
    {
        return $this->transformer->of(Student::class)->transform($this->userRepository->findStudentsInIds($ids));
    }

    public function search($data)
    {
        return $this->transformer->of(Student::class)->transform(
            $this->userRepository->search($data['search']));
    }

    public function preRegister(array $data)
    {
        $activity = $this->user->activities()->find($data['activityId']);

        if (is_null($activity))
        {
            return false;
        }

        $tempStudent = $this->userRepository->preRegisterStudent($this->user, $activity, $data);

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

    public function newStudents()
    {
        return $this->transformer->of(TempStudent::class)
            ->transform($this->userRepository->getNewStudentsOf($this->user));
    }
}
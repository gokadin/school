<?php

namespace App\Domain\Services;

use App\Domain\Events\Event;
use App\Domain\Events\Lesson;
use App\Domain\Users\Student;
use App\Domain\Users\TempStudent;
use App\Events\School\StudentPreRegistered;
use Carbon\Carbon;

class StudentService extends AuthenticatedService
{
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

    public function getProfile($id)
    {
        $student = $this->repository->of(Student::class)->find($id);

        return [
            'student' => $student,
            'registrationForm' => json_decode($student->teacher()->settings()->registrationForm(), true)
        ];
    }

    public function newStudents()
    {
        return $this->transformer->of(TempStudent::class)
            ->transform($this->repository->of(Student::class)->newStudentsOf($this->user)->toArray());
    }
}
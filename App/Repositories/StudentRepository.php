<?php

namespace App\Repositories;

use App\Domain\Users\Teacher;
use App\Domain\Users\TempStudent;
use Library\DataMapper\Collection\PersistentCollection;

class StudentRepository extends RepositoryBase
{
    public function preRegister(array $data)
    {
        $confirmationCode = md5(rand(999, 999999));

        $tempStudent = new TempStudent($data['teacher'], $data['activity'], $data['firstName'], $data['lastName'],
            $data['email'], $data['customPrice'], $confirmationCode);
        $this->dm->persist($tempStudent);

        $this->dm->flush();

        return $tempStudent;
    }

    public function findTempStudent($id)
    {
        return $this->dm->find(TempStudent::class, $id);
    }

    public function create(array $data)
    {
        throw new \Exception('Not implemented.');
    }

    public function register($student, $tempStudent)
    {
        $this->dm->persist($student->address());
        $this->dm->persist($student);

        $this->dm->delete($tempStudent);

        $this->dm->flush();
    }

    public function newStudentsOf(Teacher $teacher)
    {
        $ids = $this->dm->queryBuilder()->table('temp_students')
            ->where('teacher_id', '=', $teacher->getId())
            ->select(['id']);

        if (sizeof($ids) == 0)
        {
            return [];
        }

        return $this->dm->findIn(TempStudent::class, $ids);
    }

    public function search($string, PersistentCollection $students)
    {
        return $students->where('firstName lastName', 'LIKE', '%'.$string.'%')
            ->sortBy('firstName', true)->toArray();
    }

    public function removeExpiredTempStudents()
    {
        $this->dm->queryBuilder()->table('temp_students')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL '.TempStudent::DAYS_BEFORE_EXPIRING.' DAY)')
            ->delete();
    }
}
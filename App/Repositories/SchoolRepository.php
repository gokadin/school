<?php

namespace App\Repositories;

use Library\Database\ModelCollection;
use Library\Facades\Sentry;
use Models\Student;
use Models\Teacher;

class SchoolRepository
{
    public function getAllCurrentSchoolUsersExceptCurrent()
    {
        $schoolId = Sentry::user()->school()->id;

        $teachers = null;
        if (Sentry::is('Teacher'))
        {
            $teachers = Teacher::where('school_id', '=', $schoolId)
                ->where('id', '!=', Sentry::id())
                ->get();
        }
        else
        {
            $teachers = Teacher::where('school_id', '=', $schoolId)->get();
        }

        $students = null;
        if (Sentry::is('Student'))
        {
            $students = Student::where('school_id', '=', $schoolId)
                ->where('id', '!=', Sentry::id())
                ->get();
        }
        else
        {
            $students = Student::where('school_id', '=', $schoolId)->get();
        }

        $all = new ModelCollection();
        if ($teachers instanceof ModelCollection)
        {
            $all = $teachers;
        }
        else
        {
            $all->add($teachers);
        }

        if ($students instanceof ModelCollection)
        {
            foreach ($students as $student)
            {
                $all->add($student);
            }
        }
        else
        {
            $all->add($students);
        }

        return $all;
    }
}
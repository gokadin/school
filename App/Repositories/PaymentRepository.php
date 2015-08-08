<?php

namespace App\Repositories;

use Library\Database\Model;
use Library\Database\ModelCollection;
use Library\Facades\Sentry;
use Models\ActivityStudent;
use Models\Student;

class PaymentRepository
{
    public function prepareJsonForIndex()
    {
        $activities = Sentry::user()->activities();
        foreach ($activities as &$activity)
        {
            $activityStudents = ActivityStudent::where('activity_id', '=', $activity->id)->get();

            if ($activityStudents instanceof ActivityStudent)
            {
                $activityStudents = new ModelCollection([$activityStudents]);
            }
            else if ($activityStudents->count() == 0)
            {
                $activity->students = [];
                continue;
            }

            $studentIds = [];
            $studentCustomRates = [];
            foreach ($activityStudents as $activityStudent)
            {
                $studentIds[] = $activityStudent->student_id;
                $studentCustomRates[$activityStudent->student_id] = $activityStudent->rate;
            }

            $studentIds = implode(',', $studentIds);

            $students = Student::where('id', 'in', '('.$studentIds.')')->get();
            if ($students instanceof Student)
            {
                $students = new ModelCollection([$students]);
            }

            foreach ($students as &$student)
            {
                $student->rate = $studentCustomRates[$student->id];
            }

            $activity->students = $students;
        }

        return json_encode($activities);
    }
}
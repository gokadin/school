<?php

namespace App\Repositories;

use Library\Facades\DB;
use Library\Facades\Sentry;
use Models\Student;
use Models\StudentSetting;
use Models\Address;
use Models\ActivityStudent;

class StudentRepository
{
    public function addNewStudent(array $data)
    {
        $generatedPassword = substr(md5(rand(999, 999999)), 0, 8);

        DB::beginTransaction();

        $student = null;
        try
        {
            $student = Student::create([
                'teacher_id' => Sentry::user()->id,
                'school_id' => Sentry::user()->school()->id,
                'address_id' => Address::create()->id,
                'student_setting_id' => StudentSetting::create()->id,
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'password' => md5($generatedPassword),
                'phone' => $data['phone']
            ]);

            ActivityStudent::create([
                'activity_id' => $data['activity'],
                'student_id' => $student->id
            ]);

            DB::commit();
            return $student;
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }
}
<?php

namespace App\Repositories;

use PDOException;
use Library\Facades\DB;
use Library\Facades\Sentry;
use Models\TeacherMessageIn;
use Models\TeacherMessageOut;
use Models\StudentMessageIn;

class MessageRepository
{
    public function AddMessageFromTeacher($data)
    {
        DB::beginTransaction();

        try
        {
            TeacherMessageOut::create([
                'teacher_id' => Sentry::user()->id,
                'to_id' => $data['to_id'],
                'to_type' => $data['to_type'],
                'content' => $data['content']
            ]);

            if ($data['to_type'] == 'Teacher')
                TeacherMessageIn::create([
                    'teacher_id' => $data['to_id'],
                    'from_id' => Sentry::user()->id,
                    'from_type' => 'Teacher',
                    'content' => $data['content']
                ]);
            else if ($data['to_type'] == 'Student')
                StudentMessageIn::create([
                    'student_id' => $data['to_id'],
                    'from_id' => Sentry::user()->id,
                    'from_type' => 'Student',
                    'content' => $data['content']
                ]);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }
}
<?php

namespace App\Repositories;

use PDOException;
use Library\Facades\DB;
use Models\TeacherMessageIn;
use Models\TeacherMessageOut;
use Models\StudentMessageIn;

class MessageRepository
{
    public function addMessageFromTeacherToStudent($message, $fromId, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addTeacherMessageOut($message, $fromId, $toId, 'Student');
            $this->addStudentMessageIn($message, $toId, $fromId, 'Teacher');

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    public function addMessageFromTeacherToTeacher($message, $fromId, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addTeacherMessageOut($message, $fromId, $toId, 'Teacher');
            $this->addTeacherMessageIn($message, $toId, $fromId, 'Teacher');

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    protected function addTeacherMessageOut($message, $fromId, $toId, $toType)
    {
        TeacherMessageOut::create([
            'teacher_id' => $fromId,
            'to_id' => $toId,
            'to_type' => $toType,
            'content' => $message
        ]);
    }

    protected function addTeacherMessageIn($message, $toId, $fromId, $fromType)
    {
        TeacherMessageIn::create([
            'teacher_id' => $toId,
            'from_id' => $fromId,
            'from_type' => $fromType,
            'content' => $message
        ]);
    }

    protected function addStudentMessageIn($message, $toId, $fromId, $fromType)
    {
        StudentMessageIn::create([
            'student_id' => $toId,
            'from_id' => $fromId,
            'from_type' => $fromType,
            'content' => $message
        ]);
    }
}
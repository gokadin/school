<?php

namespace App\Repositories;

use Library\Database\ModelCollection;
use Library\Facades\Sentry;
use Models\Conversation;
use Models\ConversationMessage;
use Models\StudentMessageOut;
use Models\UserConversation;
use PDOException;
use Library\Facades\DB;
use Models\TeacherMessageIn;
use Models\TeacherMessageOut;
use Models\StudentMessageIn;
use Models\Teacher;
use Models\Student;

class MessageRepository
{
    public function addMessageFromTeacherToStudent($message, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addTeacherMessageOut($message, $toId, 'Student');
            $this->addStudentMessageIn($message, $toId);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    public function addMessageFromTeacherToTeacher($message, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addTeacherMessageOut($message, $toId, 'Teacher');
            $this->addTeacherMessageIn($message, $toId);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    public function addMessageFromStudentToTeacher($message, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addStudentMessageOut($message, $toId, 'Teacher');
            $this->addTeacherMessageIn($message, $toId);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    public function addMessageFromStudentToStudent($message, $toId)
    {
        DB::beginTransaction();

        try
        {
            $this->addStudentMessageOut($message, $toId, 'Student');
            $this->addStudentMessageIn($message, $toId);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }

    protected function addTeacherMessageOut($message, $toId, $toType)
    {
        TeacherMessageOut::create([
            'teacher_id' => Sentry::id(),
            'to_id' => $toId,
            'to_type' => $toType,
            'content' => $message
        ]);
    }

    protected function addTeacherMessageIn($message, $toId)
    {
        TeacherMessageIn::create([
            'teacher_id' => $toId,
            'from_id' => Sentry::id(),
            'from_type' => Sentry::type(),
            'content' => $message
        ]);
    }

    protected function addStudentMessageOut($message, $toId, $toType)
    {
        StudentMessageOut::create([
            'student_id' => Sentry::id(),
            'to_id' => $toId,
            'to_type' => $toType,
            'content' => $message
        ]);
    }

    protected function addStudentMessageIn($message, $toId)
    {
        StudentMessageIn::create([
            'student_id' => $toId,
            'from_id' => Sentry::id(),
            'from_type' => Sentry::type(),
            'content' => $message
        ]);
    }

    public function getInitialJsonMessages()
    {
        $messagesOut = Sentry::user()->messagesOut();
        $messagesIn = Sentry::user()->messagesIn();

        $messagesWithTeachers = array();
        $messagesWithStudents = array();
        $userMessages = array();

        foreach ($messagesOut as $out)
        {
            $message = [
                'content' => $out->content,
                'is_read' => $out->is_read == 0 ? false : true,
                'is_outgoing' => true,
                'created_at' => $out->created_at
            ];

            if ($out->to_type == 'Teacher')
                $messagesWithTeachers[$out->to_id][] = $message;
            else if ($out->to_type == 'Student')
                $messagesWithStudents[$out->to_id][] = $message;
        }

        foreach ($messagesIn as $in)
        {
            $message = [
                'content' => $in->content,
                'is_read' => $in->is_read == 0 ? false : true,
                'is_outgoing' => false,
                'created_at' => $in->created_at
            ];

            if ($in->from_type == 'Teacher')
                $messagesWithTeachers[$in->from_id][] = $message;
            else if ($in->from_type == 'Student')
                $messagesWithStudents[$in->from_id][] = $message;
        }

        $teachers = null;
        if (sizeof($messagesWithTeachers) > 0)
            $teachers = Teacher::where('id', 'in', '('.implode(',', array_keys($messagesWithTeachers)).')')->get();

        $students = null;
        if (sizeof($messagesWithStudents) > 0)
            $students = Student::where('id', 'in', '('.implode(',', array_keys($messagesWithStudents)).')')->get();

        foreach ($messagesWithTeachers as $key => $value)
        {
            $user = $teachers->where('id', '=', $key)->first();

            $userMessages['Teacher.'.$key] = [
                'id' => $key,
                'user_type' => 'Teacher',
                'name' => $user->name(),
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'show_messages' => false,
                'messages' => $value
            ];
        }

        foreach ($messagesWithStudents as $key => $value)
        {
            $user = $students->where('id', '=', $key)->first();

            $userMessages['Student.'.$key] = [
                'id' => $key,
                'user_type' => 'Student',
                'name' => $user->name(),
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'show_messages' => false,
                'new_message_content' => '',
                'messages' => $value
            ];
        }

        return json_encode($userMessages);
    }

    public function getUserConversationsJson()
    {
        $conversations = Sentry::user()->conversations();

        if (count($conversations) == 0)
        {
            return '{}';
        }

        foreach ($conversations as &$conversation)
        {
            $conversation->users = $this->getConversationUsers();
            $conversation->messages = $conversation->messages();
        }

        return json_encode($conversations);
    }

    protected function getConversationUsers($conversationId)
    {
        $users = new ModelCollection();

        $teacherIds = UserConversation::where('conversation_id', '=', $conversationId)
            ->where('user_type', '=', 'Teacher')
            ->get('user_id');
        $teachers = sizeof($teacherIds) > 0
            ? Teacher::where('id', 'in', '('.implode(',', $teacherIds).')')->get()
            : new ModelCollection();

        foreach ($teachers as $teacher)
        {
            $users->add($teacher);
        }

        $studentIds = UserConversation::where('conversation_id', '=', $conversationId)
            ->where('user_type', '=', 'Student')
            ->get('user_id');
        $students = sizeof($studentIds) > 0
            ? Student::where('id', 'in', '('.implode(',', $studentIds).')')->get()
            : new ModelCollection();

        foreach ($students as $student)
        {
            $users->add($student);
        }

        return $users;
    }

    public function addNewMessage($conversation_id, $content)
    {
        $message = new ConversationMessage();
        $message->conversation_id = $conversation_id;
        $message->from_id = Sentry::id();
        $message->from_type = Sentry::type();
        $message->content = $content;
        $message->save();
    }

    protected function addNewConversation()
    {
        return Conversation::create()->id;
    }
}
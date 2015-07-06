<?php namespace Applications\School\Modules\Teacher\Messaging;

use Library\BackController;
use Library\Facades\DB;
use Library\Facades\Page;
use Library\Facades\Request;
use Models\StudentMessage;
use Models\StudentMessageIn;
use Models\TeacherMessageIn;
use Models\TeacherMessageOut;
use Models\Student;
use Models\Teacher;

class MessagingController extends BackController
{
	public function index()
	{
        $messagesOut = TeacherMessageOut::where('teacher_id', '=', $this->currentUser->id)->get();
        $messagesIn = TeacherMessageIn::where('teacher_id', '=', $this->currentUser->id)->get();

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

            $userMessages[] = [
                'id' => $key,
                'user_type' => 'Teacher',
                'name' => $user->name(),
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'messages' => $value
            ];
        }

        foreach ($messagesWithStudents as $key => $value)
        {
            $user = $students->where('id', '=', $key)->first();

            $userMessages[] = [
                'id' => $key,
                'user_type' => 'Student',
                'name' => $user->name(),
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'messages' => $value
            ];
        }

        Page::add('userMessagesJson', json_encode($userMessages));
        Page::add('students', $this->currentUser->students());
	}

    /* AJAX */

    public function ajaxStore()
    {
        if (!$this->validateRequest([
            'to_id' => ['required', 'numeric'],
            'to_type' => 'required',
            'content' => 'required'
        ], false))
        {
            exit(false);
        }

        DB::beginTransaction();

        try
        {
            TeacherMessageOut::create([
                'teacher_id' => $this->currentUser->id,
                'to_id' => Request::data('to_id'),
                'to_type' => Request::data('to_type'),
                'content' => Request::data('content')
            ]);

            if (Request::data('to_type') == 'Teacher')
                TeacherMessageIn::create([
                    'teacher_id' => Request::data('to_id'),
                    'from_id' => $this->currentUser->id,
                    'from_type' => 'Teacher',
                    'content' => Request::data('content')
                ]);
            else if (Request::data('to_type') == 'Student')
                StudentMessageIn::create([
                    'student_id' => Request::data('to_id'),
                    'from_id' => $this->currentUser->id,
                    'from_type' => 'Student',
                    'content' => Request::data('content')
                ]);
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            exit(false);
        }

        DB::commit();
        exit(true);
    }

    public function ajaxDestroyConversation()
    {
        if (!$this->validateRequest([
            'user_id' => ['required', 'numeric'],
            'user_type' => 'required'
        ], false))
        {
            exit(false);
        }

        DB::beginTransaction();

        try
        {
            TeacherMessageOut::where('teacher_id', '=', $this->currentUser->id)
                ->where('to_id', '=', Request::data('user_id'))
                ->where('to_type', '=', Request::data('user_type'))
                ->delete();

            TeacherMessageIn::where('teacher_id', '=', $this->currentUser->id)
                ->where('from_id', '=', Request::data('user_id'))
                ->where('from_type', '=', Request::data('user_type'))
                ->delete();
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            exit(false);
        }

        DB::commit();
        exit(true);
    }
}
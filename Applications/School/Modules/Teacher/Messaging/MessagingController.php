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
        $messagesOut = TeacherMessageOut::all();
        $messagesIn = TeacherMessageIn::all();

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
            {
                $messagesWithTeachers[$out->to_id]['messages'] = $message;
                $messagesWithTeachers[$out->to_id]['user_id'] = $out->to_id;
                $messagesWithTeachers[$out->to_id]['user_type'] = 'Teacher';
            }
            else if ($out->to_type == 'Student')
            {
                $messagesWithStudents[$out->to_id]['messages'] = $message;
                $messagesWithStudents[$out->to_id]['user_id'] = $out->to_id;
                $messagesWithStudents[$out->to_id]['user_type'] = 'Student';
            }
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
            {
                $messagesWithTeachers[$in->from_id]['messages'] = $message;
                $messagesWithTeachers[$in->from_id]['user_id'] = $in->from_id;
                $messagesWithStudents[$in->from_id]['user_type'] = 'Teacher';
            }
            else if ($in->from_type == 'Student')
            {
                $messagesWithStudents[$in->from_id]['messages'] = $message;
                $messagesWithStudents[$in->from_id]['user_id'] = $in->from_id;
                $messagesWithStudents[$in->from_id]['user_type'] = 'Student';
            }
        }

        $teachers = null;
        if (sizeof($messagesWithTeachers) > 0)
        {
            $teachers = Teacher::where('id', 'in', '('.implode(',', array_keys($messagesWithTeachers)).')')->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'profile_picture'
            ]);
        }

        $students = null;
        if (sizeof($messagesWithStudents) > 0)
        {
            $students = Student::where('id', 'in', '('.implode(',', array_keys($messagesWithStudents)).')')->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'profile_picture'
            ]);
        }

        foreach ($messagesWithTeachers as $key => $value)
        {
            $user = null;
            if ($value['user_type'] == 'Teacher')
                $user = $teachers->where('id', '=', $value['user_id'])->first();
            else if ($value['user_type'] == 'Student')
                $user = $students->where('id', '=', $value['user_id'])->first();

            $userMessages[] = [
                'id' => $user->id,
                'user_type' => $value['user_type'],
                'name' => $user->name(),
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'messages' => $value['messages']
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
}
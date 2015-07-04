<?php namespace Applications\School\Modules\Teacher\Messaging;

use Library\BackController;
use Library\Facades\Page;
use Models\StudentMessage;

class MessagingController extends BackController
{
	public function index()
	{
        $students = $this->currentUser->students();

        foreach ($students as $student)
        {
            $messages = StudentMessage::where('student_id', '=', $student->id)
                ->where('recipient_type', '=', 'Teacher')
                ->where('recipient_id', '=', $this->currentUser->id)
                ->get();

            if ($messages == null)
                $messages = [];

            $student->messages = $messages;
        }

		Page::add('students', $students);
        Page::add('testStudents', $students->json());
	}

    /* AJAX */

    public function ajaxStore()
    {
        if (!$this->validateRequest([
            'recipient_id' => ['required', 'numeric'],
            'recipient_type' => 'required',
            'subject' => 'required',
            'content' => 'required'
        ], false))
        {
            exit(false);
        }

        exit(TeacherMessage::create([
            'teacher_id' => $this->currentUser->id,
            'recipient_id' => Request::data('recipient_id'),
            'recipient_type' => Request::data('recipient_type'),
            'subject' => Request::data('subject'),
            'content' => Request::data('content')
        ]) != null);
    }
}
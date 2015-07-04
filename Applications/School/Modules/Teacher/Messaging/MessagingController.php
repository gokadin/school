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

    public function getUsers()
    {
        $students = $this->currentUser->students();

        foreach ($students as $student)
        {
            $student->name = $student->name();

            $temp = array();
            $messages = StudentMessage::where('student_id', '=', $student->id)
                ->where('recipient_type', '=', 'Teacher')
                ->where('recipient_id', '=', $this->currentUser->id)
                ->get();

            if ($messages != null)
            {
                foreach ($messages as $message)
                {
                    $temp[] = [
                        'subject' => $message->subject,
                        'content' => $message->content
                    ];
                }
            }

            $student->messages = $temp;
        }

        exit($students->json());
    }
}
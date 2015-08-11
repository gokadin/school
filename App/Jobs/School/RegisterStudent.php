<?php

namespace App\Jobs\School;

use App\Events\School\StudentRegistered;
use App\Jobs\Job;
use App\Repositories\StudentRepository;
use Library\Events\FiresEvents;
use Library\Facades\Session;

class RegisterStudent extends Job
{
    use FiresEvents;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle(StudentRepository $studentRepository)
    {
        $student = $studentRepository->addNewStudent($this->data);

        if (!$student)
        {
            Session::setFlash('Failed to register student. Please try again.', 'error');
            return;
        }

        Session::setFlash('Student <b>'.$student->name.'</b> was added successfully.');

        $this->fireEvent(new StudentRegistered($student));
    }
}
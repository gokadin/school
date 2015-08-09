<?php

namespace App\Events\School;

use App\Events\Event;
use Models\Student;

class StudentRegistered extends Event
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function student()
    {
        return $this->student;
    }
}
<?php

namespace App\Events\Frontend;

use App\Events\Event;
use App\Domain\Users\Student;

class StudentLoggedIn extends Event
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
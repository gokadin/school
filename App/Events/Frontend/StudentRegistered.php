<?php

namespace App\Events\Frontend;

use App\Domain\Users\Student;
use App\Events\Event;

class StudentRegistered extends Event
{
    /**
     * @var Student
     */
    private $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function student()
    {
        return $this->student;
    }
}
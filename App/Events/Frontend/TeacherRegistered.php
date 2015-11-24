<?php

namespace App\Events\Frontend;

use App\Domain\Users\Teacher;
use App\Events\Event;

class TeacherRegistered extends Event
{
    protected $teacher;

    public function __construct(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function teacher()
    {
        return $this->teacher;
    }
}
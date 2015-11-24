<?php

namespace App\Events\Frontend;

use App\Events\Event;
use App\Domain\Users\Teacher;

class TeacherLoggedIn extends Event
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
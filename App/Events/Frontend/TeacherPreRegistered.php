<?php

namespace App\Events\Frontend;

use App\Events\Event;

class TeacherPreRegistered extends Event
{
    protected $teacher;

    public function __construct($teacher)
    {
        $this->teacher = $teacher;
    }

    public function teacher()
    {
        return $this->teacher;
    }
}
<?php

namespace App\Events\Frontend;

use App\Domain\Users\TempTeacher;
use App\Events\Event;

class TeacherPreRegistered extends Event
{
    protected $tempTeacher;

    public function __construct(TempTeacher $tempTeacher)
    {
        $this->tempTeacher = $tempTeacher;
    }

    public function tempTeacher()
    {
        return $this->tempTeacher;
    }
}
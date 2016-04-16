<?php

namespace App\Events\Frontend;

use App\Domain\Users\TempTeacher;
use Library\Events\Event;

class TeacherPreRegistered extends Event
{
    private $tempTeacher;

    public function __construct(TempTeacher $tempTeacher)
    {
        $this->tempTeacher = $tempTeacher;
    }

    public function tempTeacher()
    {
        return $this->tempTeacher;
    }
}
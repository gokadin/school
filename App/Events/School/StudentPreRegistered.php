<?php

namespace App\Events\School;

use App\Domain\Users\TempStudent;
use Library\Events\Event;

class StudentPreRegistered extends Event
{
    protected $tempStudent;

    public function __construct(TempStudent $tempStudent)
    {
        $this->tempStudent = $tempStudent;
    }

    public function tempStudent()
    {
        return $this->tempStudent;
    }
}
<?php

namespace App\Events\Frontend;

use App\Events\Event;
use Library\Events\ShouldBroadcast;

class TeacherPreRegistered extends Event implements ShouldBroadcast
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

    public function broadcastOn()
    {
        return [
            'test'
        ];
    }
}
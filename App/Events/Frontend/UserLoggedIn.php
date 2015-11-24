<?php

namespace App\Events\Frontend;

use App\Events\Event;

class UserLoggedIn extends Event
{
    protected $user;
    protected $type;

    public function __construct($user, $type)
    {
        $this->user = $user;
        $this->type = $type;
    }

    public function user()
    {
        return $this->user;
    }

    public function type()
    {
        return $this->type;
    }
}
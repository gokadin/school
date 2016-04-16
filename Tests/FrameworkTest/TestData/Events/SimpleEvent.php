<?php

namespace Tests\FrameworkTest\TestData\Events;

use Library\Events\Event;

class SimpleEvent extends Event
{
    protected $fired;
    protected $secondFired;

    public function __construct()
    {
        $this->fired = false;
        $this->secondFired = false;
    }

    public function fired()
    {
        $this->fired = true;
    }

    public function secondFired()
    {
        $this->secondFired = true;
    }

    public function hasFired()
    {
        return $this->fired;
    }

    public function secondHasFired()
    {
        return $this->secondFired;
    }
}
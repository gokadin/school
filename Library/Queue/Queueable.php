<?php

namespace Library\Queue;

trait Queueable
{
    protected $delay = 0;
    protected $maxAttempts = 3;

    public function setDelay($seconds)
    {
        $this->delay = $seconds;
        return $this;
    }

    public function getDelay()
    {
        return $this->delay;
    }

    public function setMaxAttempts($number)
    {
        $this->maxAttempts = $number;
        return $this;
    }

    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }
}
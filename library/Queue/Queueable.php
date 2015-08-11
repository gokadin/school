<?php

namespace Library\Queue;

trait Queueable
{
    public $delay = 0;
    public $maxAttempts = 3;

    public function delay($seconds)
    {
        $this->delay = $seconds;
        return $this;
    }

    public function maxAttempts($number)
    {
        $this->maxAttempts = $number;
        return $this;
    }
}
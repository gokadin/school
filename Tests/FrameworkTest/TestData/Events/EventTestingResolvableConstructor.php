<?php

namespace Tests\FrameworkTest\TestData\Events;

class EventTestingResolvableConstructor
{
    protected $fired;
    protected $resolvedParameter;

    public function __construct()
    {
        $this->fired = false;
        $this->resolvedParameter = null;
    }

    public function fired()
    {
        $this->fired = true;
    }

    public function hasFired()
    {
        return $this->fired;
    }

    public function setResolvedParameter($value)
    {
        $this->resolvedParameter = $value;
    }

    public function resolvedParameter()
    {
        return $this->resolvedParameter;
    }
}
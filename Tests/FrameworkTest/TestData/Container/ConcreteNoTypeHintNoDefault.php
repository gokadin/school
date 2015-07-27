<?php

namespace Tests\FrameworkTest\TestData\Container;

class ConcreteNoTypeHintNoDefault
{
    protected $a;

    public function __construct($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }
}
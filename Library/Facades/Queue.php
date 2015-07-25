<?php

namespace Library\Facades;

class Queue extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'queue';
    }
}
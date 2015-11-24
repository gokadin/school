<?php

namespace Library\Facades;

class Event extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eventManager';
    }
}
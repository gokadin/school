<?php

namespace Library\Facades;

class Sentry extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'sentry';
    }
}
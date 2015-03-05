<?php namespace Library\Facades;

class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}
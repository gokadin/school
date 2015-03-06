<?php namespace Library\Facades;

class DB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}
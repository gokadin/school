<?php namespace Library\Facades;

class App extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'app';
    }
}
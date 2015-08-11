<?php

namespace Library\Facades;

class ViewFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'viewFactory';
    }
}
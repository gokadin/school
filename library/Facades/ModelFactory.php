<?php

namespace Library\Facades;

class ModelFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'modelFactory';
    }
}
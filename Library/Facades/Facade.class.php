<?php namespace Library\Facades;

use RuntimeException;

abstract class Facade
{
    protected static $app;
    protected static $resolvedInstance;

    protected static function getFacadeAccessor()
    {
        throw new RuntimeException("Facade does not implement getFacadeAccessor method.");
    }

    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    public static function resolveFacadeInstance($name)
    {
        if (is_object($name))
            return $name;

        if (isset(static::$resolvedInstance[$name]))
            return static::$resolvedInstance[$name];

        return static::$resolvedInstance[$name] = static::$app[$name];
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        switch (count($args))
        {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($args[0]);
            case 2:
                return $instance->$method($args[0], $args[1]);
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}
<?php namespace Library\Facades;

use RuntimeException;
use Mockery;

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

    public static function resolveFacadeInstance($name)
    {
        if (is_object($name))
            return $name;

        if (isset(static::$resolvedInstance[$name]))
            return static::$resolvedInstance[$name];

        return static::$resolvedInstance[$name] = static::$app->container()->make($name);
    }

    public static function resetResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

    public static function instance()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

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

    public static function shouldReceive($methodName)
    {
        $class = get_class(static::resolveFacadeInstance(static::getFacadeAccessor()));

        $mock = $class ? Mockery::mock($class) : Mockery::mock();

        static::$resolvedInstance[static::getFacadeAccessor()] = $mock;

        return $mock->shouldReceive($methodName);
    }
}
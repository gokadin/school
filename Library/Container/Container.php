<?php namespace Library\Container;

use ArrayAccess;

class Container implements ArrayAccess
{
    protected $resolved = [];
    protected $instances = [];

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    public function make($abstract, $parameters = [])
    {
        if (isset($this->instances[$abstract]))
        {
            return $this->instances[$abstract];
        }
    }

    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
    }

    public function offsetGet($key)
    {
        return $this->make($key);
    }

    public function offsetSet($key, $value)
    {
        $this->instance($key, $value);
    }

    public function offsetExists($key)
    {
        return isset($this->instances[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->instances[$key]);
    }

    public function __get($key)
    {
        return $this[$key];
    }

    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}
<?php

namespace Library\Redis;

use JsonSerializable;

class RedisModel implements JsonSerializable
{
    protected $vars;
    protected $hidden = [];

    public function __construct($data = [])
    {
        $this->vars = $data;
    }

    public function __set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
        {
            return $this->vars[$var];
        }
    }

    public function __isset($var)
    {
        return isset($this->vars[$var]);
    }

    public function getSerializableProperties()
    {
        if (sizeof($this->hidden) == 0)
        {
            return $this->vars;
        }

        $vars = [];
        foreach ($this->vars as $key => $value)
        {
            if (in_array($key, $this->hidden))
            {
                continue;
            }

            $this->vars[$key] = $value;
        }

        return $vars;
    }

    function jsonSerialize()
    {
        return $this->getSerializableProperties();
    }

    public function __sleep()
    {
        return array('vars');
    }
}
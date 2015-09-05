<?php

namespace Library\Redis;

use Predis\Client as PredisClient;

class Redis
{
    protected $predis;

    public function __construct()
    {
        $this->predis = new PredisClient();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->predis, $name], $arguments);
    }

    public function getRedis()
    {
        return $this->predis;
    }
}
<?php

namespace Library\Redis;

use Predis\Client as PredisClient;

class Redis
{
    protected $predis;

    public function __construct($host = '10.0.0.1', $port = 6378)
    {
        $this->predis = new PredisClient([
            'scheme' => 'tcp',
            'host' => $host,
            'port' => $port
        ]);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->predis, $name], $arguments);
    }
}
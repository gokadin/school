<?php

namespace Library\Redis;

use Predis\Client as PredisClient;

class Redis
{
    protected $predis;

    public function __construct($settings)
    {
        $this->predis = new PredisClient([
            'database' => $settings['redis']['database']
        ]);
    }

    public function getRedis()
    {
        return $this->predis;
    }
}
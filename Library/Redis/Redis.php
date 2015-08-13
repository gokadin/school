<?php

namespace Library\Redis;

use Predis\Client as PredisClient;

class Redis
{
    protected $predis;

    public function __construct()
    {
        $database = 0;
        if (env('APP_ENV') == 'testing' || env('APP_ENV') == 'framework_testing')
        {
            $database = 1;
        }

        $this->predis = new PredisClient([
            'database' => $database
        ]);
    }

    public function getRedis()
    {
        return $this->predis;
    }
}
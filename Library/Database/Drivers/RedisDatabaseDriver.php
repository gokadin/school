<?php

namespace Library\Database\Drivers;

use Predis\Client as PredisClient;

class RedisDatabaseDriver implements IDatabaseDriver
{
    protected $redis;

    public function __construct(PredisClient $redis)
    {
        $this->redis = $redis;
    }

    public function persist($object)
    {

    }
}
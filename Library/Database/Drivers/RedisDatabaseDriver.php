<?php

namespace Library\Database\Drivers;

use Predis\Client as PredisClient;

class RedisDatabaseDriver implements IDatabaseDriver
{
    protected $redis;

    public function __construct($settings)
    {
        $this->redis = new PredisClient([
            'database' => $settings['database']
        ]);
    }

    public function persist($object)
    {

    }
}
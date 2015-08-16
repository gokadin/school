<?php

namespace Library\Console\Modules\DataMapper;

use Library\Database\Schema;
use Predis\Client as PredisClient;

class RedisCacheDriver
{
    public function __construct()
    {
        $this->redis = new PredisClient([
            'database' => 32
        ]);
    }

    public function loadSchema(Schema $schema)
    {
        foreach ($schema->tables() as $table)
        {
            
        }
    }
}
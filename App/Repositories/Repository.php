<?php

namespace App\Repositories;

use Library\DataMapper\DataMapper;
use Library\Log\Log;

class Repository
{
    protected $dm;
    protected $log;

    public function __construct(DataMapper $dm, Log $log)
    {
        $this->dm = $dm;
        $this->log = $log;
    }
}
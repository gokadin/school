<?php

namespace App\Repositories;

use Library\DataMapper\DataMapper;

class Repository
{
    protected $dm;

    public function __construct(DataMapper $dm)
    {
        $this->dm = $dm;
    }
}
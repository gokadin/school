<?php

namespace App\Repositories;

use Library\DataMapper\DataMapper;
use Library\Log\Log;

abstract class AuthenticatedRepository extends Repository
{
    protected $user;

    public function __construct(DataMapper $dm, Log $log, UserRepository $userRepository)
    {
        parent::__construct($dm, $log);

        $this->user = $userRepository->getLoggedInUser();
    }
}
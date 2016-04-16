<?php

namespace App\Domain\Services;

use App\Domain\Users\Authenticator;
use App\Domain\Users\User;
use App\Repositories\Repository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class AuthenticatedService extends Service
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(EventManager $eventManager, Repository $repository, Authenticator $authenticator)
    {
        parent::__construct($eventManager, $repository);

        $this->user = $authenticator->user();
    }
}
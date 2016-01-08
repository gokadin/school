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

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                Repository $repository, Authenticator $authenticator)
    {
        parent::__construct($queue, $eventManager, $transformer, $repository);

        $this->user = $authenticator->user();
    }
}
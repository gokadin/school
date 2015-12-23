<?php

namespace App\Domain\Services;

use App\Repositories\UserRepository;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Library\Transformer\Transformer;

class AuthenticatedService extends Service
{
    /**
     * @var \App\Domain\Users\User
     */
    protected $user;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    public function __construct(Queue $queue, EventManager $eventManager, Transformer $transformer,
                                UserRepository $userRepository)
    {
        parent::__construct($queue, $eventManager, $transformer);

        $this->user = $userRepository->getLoggedInUser();
        $this->userRepository = $userRepository;
    }
}
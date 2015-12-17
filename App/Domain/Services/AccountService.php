<?php

namespace App\Domain\Services;

use App\Repositories\UserRepository;
use Library\Queue\Queue;

class AccountService extends Service
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Queue $queue, UserRepository $userRepository)
    {
        parent::__construct($queue);

        $this->userRepository = $userRepository;
    }

    public function updatePersonalInfo(array $data)
    {
        $this->userRepository->updatePersonalInfo($data);

        return true;
    }

    public function updatePassword(array $data)
    {
        $user = $this->userRepository->getLoggedInUser();

        if ($user->password() != md5($data['currentPassword']))
        {
            return false;
        }

        $this->userRepository->updatePassword(md5($data['newPassword']));

        return true;
    }
}
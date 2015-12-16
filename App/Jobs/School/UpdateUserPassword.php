<?php

namespace App\Jobs\School;

use App\Jobs\Job;
use App\Repositories\UserRepository;
use Library\Queue\ShouldQueue;

class UpdateUserPassword extends Job implements ShouldQueue
{
    private $currentPassword;
    private $newPassword;

    public function __construct($currentPassword, $newPassword)
    {
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
    }

    public function currentPassword()
    {
        return $this->currentPassword;
    }

    public function newPassword()
    {
        return $this->newPassword;
    }

    public function handle(UserRepository $userRepository)
    {
        $userRepository->updatePassword($this->currentPassword, $this->newPassword);
    }
}
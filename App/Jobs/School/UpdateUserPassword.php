<?php

namespace App\Jobs\School;

use App\Domain\Users\Teacher;
use App\Jobs\Job;
use App\Repositories\Repository;
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

    public function handle(Repository $repository)
    {
        $repository->of(Teacher::class)->updatePassword($this->currentPassword, $this->newPassword);
    }
}
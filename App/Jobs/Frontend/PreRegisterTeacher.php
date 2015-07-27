<?php

namespace App\Jobs\Frontend;

use App\Jobs\Job;
use App\Repositories\Contracts\IUserRepository;
use Library\Queue\ShouldQueue;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(IUserRepository $userRepository)
    {
        $userRepository->preRegisterTeacher($this->data);

        // fire event
    }
}
<?php

namespace App\Jobs\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Jobs\Job;
use App\Repositories\Contracts\IUserRepository;
use Library\Events\FiresEvents;
use Library\Facades\Log;
use Library\Queue\JobFailedException;
use Library\Queue\ShouldQueue;

use Models\TempTeacher;

class PreRegisterTeacher extends Job implements ShouldQueue
{
    use FiresEvents;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(IUserRepository $userRepository)
    {
//        $tempTeacher = $userRepository->preRegisterTeacher($this->data);
//
//        if (!$tempTeacher)
//        {
//            throw new JobFailedException('Could not register temp teacher. Repository returned false.');
//            return;
//        }

        $tempTeacher = TempTeacher::create([
            'subscription_id' => 1,
            'first_name' => 'jake',
            'last_name' => 'popo',
            'email' => 'a@b.com',
            'confirmation_code' => '123'
        ]);
        Log::info('fired from job...');
        $this->fireEvent(new TeacherPreRegistered($tempTeacher));
    }
}
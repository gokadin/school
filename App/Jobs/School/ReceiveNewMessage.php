<?php

namespace App\Jobs\School;

use App\Events\School\NewMessageReceived;
use App\Jobs\Job;
use App\Repositories\MessageRepository;
use Library\Events\FiresEvents;
use Library\Facades\Sentry;

class ReceiveNewMessage extends Job
{
    use FiresEvents;

    protected $message;
    protected $toId;
    protected $toType;

    public function __construct($message, $toId, $toType)
    {
        $this->message = $message;
        $this->toId = $toId;
        $this->toType = $toType;
    }

    public function handle(MessageRepository $messageRepository)
    {
        $this->fireEvent(new NewMessageReceived($this->message, $this->toId, $this->toType));

        $this->addToRepository($messageRepository);
    }

    protected function addToRepository(MessageRepository $messageRepository)
    {
        switch (Sentry::type())
        {
            case 'Teacher':
                if ($this->toType == 'Teacher')
                {
                    $messageRepository->addMessageFromTeacherToTeacher($this->message, Sentry::user()->id, $this->toId);
                }
                else if ($this->toType == 'Student')
                {
                    $messageRepository->addMessageFromTeacherToStudent($this->message, Sentry::user()->id, $this->toId);
                }
                break;
        }
    }
}
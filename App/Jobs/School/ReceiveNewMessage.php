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

    protected $conversationId;
    protected $message;
    protected $toId;
    protected $toType;

    public function __construct($conversationId, $message, $toId, $toType)
    {
        $this->conversationId = $conversationId;
        $this->message = $message;
        $this->toId = $toId;
        $this->toType = $toType;
    }

    public function handle(MessageRepository $messageRepository)
    {
        $this->fireEvent(new NewMessageReceived($this->message, $this->toId, $this->toType));

        $this->addToRepository2($messageRepository);
    }

    protected function addToRepository2(MessageRepository $messageRepository)
    {
        $messageRepository->addNewMessage($this->conversationId, $this->message);
    }

    protected function addToRepository(MessageRepository $messageRepository)
    {
        switch (Sentry::type())
        {
            case 'Teacher':
                if ($this->toType == 'Teacher')
                {
                    $messageRepository->addMessageFromTeacherToTeacher($this->message, $this->toId);
                }
                else if ($this->toType == 'Student')
                {
                    $messageRepository->addMessageFromTeacherToStudent($this->message, $this->toId);
                }
                break;
            case 'Student':
                if ($this->toType == 'Teacher')
                {
                    $messageRepository->addMessageFromStudentToTeacher($this->message, $this->toId);
                }
                else if ($this->toType == 'Student')
                {
                    $messageRepository->addMessageFromStudentToStudent($this->message, $this->toId);
                }
            break;
        }
    }
}
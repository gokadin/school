<?php

namespace App\Jobs\School;

use App\Events\School\NewMessageReceived;
use App\Jobs\Job;
use App\Repositories\MessageRepository;
use Carbon\Carbon;
use Library\Events\FiresEvents;
use Library\Facades\Sentry;

class ReceiveNewMessage extends Job
{
    use FiresEvents;

    protected $conversationId;
    protected $content;
    protected $created_at;

    public function __construct($conversationId, $content)
    {
        $this->conversationId = $conversationId;
        $this->content = $content;
        $this->created_at = Carbon::now();
    }

    public function handle(MessageRepository $messageRepository)
    {
        $this->fireEvent(new NewMessageReceived($this->conversationId, $this->content, $this->created_at));

        $this->addToRepository2($messageRepository);
    }

    protected function addToRepository2(MessageRepository $messageRepository)
    {
        $messageRepository->addNewMessage($this->conversationId, $this->content);
    }

    protected function addToRepository(MessageRepository $messageRepository)
    {
        switch (Sentry::type())
        {
            case 'Teacher':
                if ($this->toType == 'Teacher')
                {
                    $messageRepository->addMessageFromTeacherToTeacher($this->content, $this->toId);
                }
                else if ($this->toType == 'Student')
                {
                    $messageRepository->addMessageFromTeacherToStudent($this->content, $this->toId);
                }
                break;
            case 'Student':
                if ($this->toType == 'Teacher')
                {
                    $messageRepository->addMessageFromStudentToTeacher($this->content, $this->toId);
                }
                else if ($this->toType == 'Student')
                {
                    $messageRepository->addMessageFromStudentToStudent($this->content, $this->toId);
                }
            break;
        }
    }
}
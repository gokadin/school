<?php

namespace App\Http\Controllers\School\Common;

use App\Http\Controllers\Controller;
use App\Repositories\MessageRepository;
use App\Repositories\SchoolRepository;
use App\Http\Requests\School\StoreMessageRequest;
use App\Jobs\School\ReceiveNewMessage;

class MessagingController extends Controller
{
    public function index(MessageRepository $messageRepository, SchoolRepository $schoolRepository)
    {
        return view('school.common.messaging.index', [
            'conversationsJson' => $messageRepository->getUserConversationsJson(),
            'students' => $schoolRepository->getAllCurrentSchoolUsersExceptCurrent()
        ]);
    }

    public function ajaxStore(StoreMessageRequest $request)
    {
        $this->dispatchJob(new ReceiveNewMessage($request->conversation_id, $request->content));
    }
}
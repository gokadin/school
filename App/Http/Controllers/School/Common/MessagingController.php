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
        $x = $messageRepository->getUserConversationsJson();

        return view('school.common.messaging.index2', [
            'conversationsJson' => $messageRepository->getUserConversationsJson(),
            'students' => $schoolRepository->getAllCurrentSchoolUsersExceptCurrent()
        ]);
    }

    public function ajaxStore(StoreMessageRequest $request)
    {
        $this->dispatchJob(new ReceiveNewMessage($request->content, $request->to_id, $request->to_type));
    }
}
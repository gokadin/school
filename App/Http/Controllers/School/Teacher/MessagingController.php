<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use Library\Facades\DB;
use Library\Facades\Page;
use Library\Facades\Request;
use Models\StudentMessage;
use Models\TeacherMessageIn;
use Models\TeacherMessageOut;

class MessagingController extends Controller
{
    /* AJAX */



    public function ajaxDestroyConversation()
    {
        if (!$this->validateRequest([
            'user_id' => ['required', 'numeric'],
            'user_type' => 'required'
        ], false))
        {
            exit(false);
        }

        DB::beginTransaction();

        try
        {
            TeacherMessageOut::where('teacher_id', '=', $this->currentUser->id)
                ->where('to_id', '=', Request::data('user_id'))
                ->where('to_type', '=', Request::data('user_type'))
                ->delete();

            TeacherMessageIn::where('teacher_id', '=', $this->currentUser->id)
                ->where('from_id', '=', Request::data('user_id'))
                ->where('from_type', '=', Request::data('user_type'))
                ->delete();
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            exit(false);
        }

        DB::commit();
        exit(true);
    }
}
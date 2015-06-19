<?php namespace Applications\School\Modules\Teacher\Ajax;

error_reporting(0);

use Library\BackController;
use Library\Facades\Request;
use Models\User;
use Models\Event;
use Carbon\Carbon;

class AjaxController extends BackController
{
    public function emailExists()
    {
        echo User::exists('email', Request::postData('email'));
    }
    
    public function addEvent()
    {
        $event = new Event();
        $event->user_id = $this->currentUser->id;
        $event->title = empty(Request::postData('title')) ? 'untitled' : Request::postData('title');
        $event->start_date = new Carbon(Request::postData('startDate'));
        $event->end_date = new Carbon(Request::postData('endDate'));
        $event->is_all_day = Request::postData('isAllDay');
        $event->start_time = empty(Request::postData('startTime')) ?  '12:00pm' : Request::postData('startTime');
        $event->end_time = empty(Request::postData('endTime')) ?  '12:00pm' : Request::postData('endTime');
        $event->is_recurring = Request::postData('isRecurring');
        $event->recurring_repeat = Request::postData('recurringRepeat');
        $event->recurring_every = Request::postData('recurringEvery');
        $event->is_recurring_ends_never = Request::postData('isRecurringEndsNever');
        $event->recurring_end_date = new Carbon(Request::postData('recurringEndDate'));
        $event->description = Request::postData('description');
        $event->color = Request::postData('color');
        $event->location = Request::postData('location');
        $event->visibility = Request::postData('visibility');
        $event->studentIds = empty(Request::postData('studentIds')) ? "0" : implode(',', Request::postData('studentIds'));
        $event->activity_id = empty(Request::postData('activity_id')) ? 0 : Request::postData('activityId');
        $event->notifyMeBy = Request::postData('notifyMeBy');
        $event->notifyMeBefore = Request::postData('notifyMeBefore');
        $event->save();

        echo true;
    }
}

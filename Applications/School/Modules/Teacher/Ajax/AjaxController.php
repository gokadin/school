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
        echo User::exists('email', Request::data('email'));
    }
    
    public function addEvent()
    {
        $event = new Event();
        $event->user_id = $this->currentUser->id;
        $event->title = empty(Request::data('title')) ? 'untitled' : Request::data('title');
        $event->start_date = new Carbon(Request::data('startDate'));
        $event->end_date = new Carbon(Request::data('endDate'));
        $event->is_all_day = Request::data('isAllDay');
        $event->start_time = empty(Request::data('startTime')) ?  '12:00pm' : Request::data('startTime');
        $event->end_time = empty(Request::data('endTime')) ?  '12:00pm' : Request::data('endTime');
        $event->is_recurring = Request::data('isRecurring');
        $event->recurring_repeat = Request::data('recurringRepeat');
        $event->recurring_every = Request::data('recurringEvery');
        $event->is_recurring_ends_never = Request::data('isRecurringEndsNever');
        $event->recurring_end_date = new Carbon(Request::data('recurringEndDate'));
        $event->description = Request::data('description');
        $event->color = Request::data('color');
        $event->location = Request::data('location');
        $event->visibility = Request::data('visibility');
        $event->studentIds = empty(Request::data('studentIds')) ? "0" : implode(',', Request::data('studentIds'));
        $event->activity_id = empty(Request::data('activity_id')) ? 0 : Request::data('activityId');
        $event->notifyMeBy = Request::data('notifyMeBy');
        $event->notifyMeBefore = Request::data('notifyMeBefore');
        $event->save();

        echo true;
    }
}

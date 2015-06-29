<?php namespace Applications\School\Modules\Teacher\Ajax;

error_reporting(0);

use Library\BackController;
use Library\Facades\DB;
use Library\Facades\Request;
use Library\Facades\Validator;
use Models\TeacherEvent;
use Carbon\Carbon;

class AjaxController extends BackController
{
    public function emailExists()
    {
        // broken ******************************************
        echo User::exists('email', Request::data('email'));
    }
    
    public function addEvent()
    {
        $event = TeacherEvent::create([
            'teacher_id' => $this->currentUser->id,
            'title' => empty(Request::data('title')) ? 'untitled' : Request::data('title'),
            'start_date' => new Carbon(Request::data('startDate')),
            'end_date' => new Carbon(Request::data('endDate')),
            'is_all_day' => Request::data('isAllDay'),
            'start_time' => empty(Request::data('startTime')) ?  '12:00pm' : Request::data('startTime'),
            'end_time' => empty(Request::data('endTime')) ?  '12:00pm' : Request::data('endTime'),
            'is_recurring' => Request::data('isRecurring'),
            'recurring_repeat' => Request::data('recurringRepeat'),
            'recurring_every' => Request::data('recurringEvery'),
            'is_recurring_ends_never' => Request::data('isRecurringEndsNever'),
            'recurring_end_date' => new Carbon(Request::data('recurringEndDate')),
            'description' => Request::data('description'),
            'color' => Request::data('color'),
            'location' => Request::data('location'),
            'visibility' => Request::data('visibility'),
            'student_ids' => empty(Request::data('studentIds')) ? '0' : implode(',', Request::data('studentIds')),
            'activity_id' => empty(Request::data('activityId')) ? 0 : Request::data('activityId'),
            'notify_me_by' => Request::data('notifyMeBy'),
            'notify_me_before' => Request::data('notifyMeBefore')
        ]);

        $javascriptValues = [
            'id' => $event->id,
            'title' => $event->title,
            'startDate' => $event->start_date,
            'endDate' => $event->endDate,
            'color' => $event->color
        ];

        echo json_encode($javascriptValues);
    }

    public function changeEventDate()
    {
        if (!Request::dataExists('eventId') ||
            !Request::dataExists('startDate') ||
            !Request::dataExists('endDate'))
            return;

        $event = TeacherEvent::find(Request::data('eventId'));
        if ($event == null)
            return;

        $event->start_date = new Carbon(Request::data('startDate'));
        $event->end_date = new Carbon(Request::data('endDate'));
        if (Request::dataExists('startTime') && !empty(Request::data('startTime')))
            $event->start_time = Request::data('startTime');
        if (Request::dataExists('endTime') && !empty(Request::data('endTime')))
            $event->end_date = Request::data('endDate');

        $event->save();
    }
}

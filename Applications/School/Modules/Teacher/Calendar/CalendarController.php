<?php namespace Applications\School\Modules\Teacher\Calendar;

use Library\BackController;
use Library\Facades\Page;

class CalendarController extends BackController
{
	public function index()
	{
		Page::add('students', $this->currentUser->students());
		Page::add('activities', $this->currentUser->activities());

        $events = $this->currentUser->events();
        $jsEvents = array();
        foreach ($events as $event)
        {
            $temp = array();
            $temp['id'] = $event->id;
            $temp['title'] = $event->title;
            $temp['startDate'] = $event->start_date;
            $temp['color'] = $event->color;

            $jsEvents[] = $temp;
        }

        Page::add('serializedEvents', json_encode($jsEvents));
	}
}
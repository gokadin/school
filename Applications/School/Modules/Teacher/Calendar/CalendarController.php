<?php namespace Applications\School\Modules\Teacher\Calendar;

use Library\BackController;
use Library\Facades\Page;

class CalendarController extends BackController
{
	public function index()
	{
		Page::add('students', $this->currentUser->students());
		Page::add('activities', $this->currentUser->activities());
        $x = $this->currentUser->events();
        Page::add('events', $this->currentUser->events());
	}
}
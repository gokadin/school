<?php namespace Applications\School\Modules\Teacher\Calendar;

use Library\BackController;
use Library\Facades\Page;
use Library\Facades\Request;

class CalendarController extends BackController
{
	public function index()
	{
		Page::add('students', $this->currentUser->students());
		Page::add('activities', $this->currentUser->activities());
	}
}
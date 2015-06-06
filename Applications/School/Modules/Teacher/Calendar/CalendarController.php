<?php namespace Applications\School\Modules\Teacher\Calendar;

use Library\BackController;
use Library\Facades\Page;

class CalendarController extends BackController
{
	public function index()
	{
		$studentNames = array();
		foreach ($this->currentUser->students() as $student)
		{
			$studentNames[] = $student->name();
		}
		
		Page::add('studentNames', $studentNames);
		Page::add('activities', $this->currentUser->activities());
	}
}
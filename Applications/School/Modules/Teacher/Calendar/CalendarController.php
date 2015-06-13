<?php namespace Applications\School\Modules\Teacher\Calendar;

use Library\BackController;
use Library\Facades\Page;
use Library\Facades\Request;

class CalendarController extends BackController
{
	public function index()
	{	
		$studentNamesAndIds = array();
		foreach ($this->currentUser->students() as $student)
		{
			$studentNamesAndIds[$student->id] = $student->name();
		}
		
		Page::add('studentNamesAndIds', $studentNamesAndIds);
		Page::add('activities', $this->currentUser->activities());
	}
}
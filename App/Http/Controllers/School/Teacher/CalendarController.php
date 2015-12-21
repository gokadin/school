<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.calendar.index');
    }
}
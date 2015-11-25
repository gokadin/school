<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.activity.index');
    }

    public function create()
    {
        return $this->view->make('school.teacher.activity.create');
    }
}
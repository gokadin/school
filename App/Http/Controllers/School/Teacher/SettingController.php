<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.setting.index');
    }

    public function registrationForm()
    {
        return $this->view->make('school.teacher.setting.registrationForm');
    }
}
<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function schoolInformation()
    {
        return $this->view->make('school.teacher.setting.schoolInformation');
    }

    public function registrationForm()
    {
        return $this->view->make('school.teacher.setting.registrationForm');
    }

    public function preferences()
    {
        return $this->view->make('school.teacher.setting.preferences');
    }
}
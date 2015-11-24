<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.index.index');
    }
}
<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->view->make('school.student.index.index');
    }
}
<?php

namespace App\Http\Controllers\School\Student;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('school.student.index.index');
    }
}
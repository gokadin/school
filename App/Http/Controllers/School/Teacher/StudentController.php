<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\School\RegisterStudent;
use App\Http\Requests\School\StoreStudentRequest;
use Library\Facades\Redirect;
use Library\Facades\Sentry;
use Library\Facades\Session;
use Library\Facades\Request;
use Library\Facades\Response;
use Models\Student;
use Models\UserSetting;

class StudentController extends Controller
{
    public function index()
    {
        return view('school.teacher.student.index', [
            'students' => Sentry::user()->students()
        ]);
    }

    public function create()
    {
        return view('school.teacher.student.create', [
            'activities' => json_encode(Sentry::user()->activities())
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $this->dispatchJob(new RegisterStudent($request->all()));

        if (Request::data('createAnother') == 1)
            Redirect::to('school.teacher.student.create');

        Redirect::to('school.teacher.student.index');
    }

    public function update()
    {
        if (!is_numeric(Request::data('studentId')) || !Student::exists('id', Request::data('studentId')))
        {
            Session::setFlash('An error occurred. Student is not valid.');
            Response::toAction('School#Teacher/Student#index');
        }

        $student = Student::find(Request::data('studentId'));
        $student->first_name = Request::data('firstName');
        $student->last_name = Request::data('lastName');

        if ($student->save())
            Session::setFlash('Updated student <b>'.$student->name().'</b>.');
        else
            Session::setFlash('An error occurred. Could not update student.');

        Response::toAction('School#Teacher/Student#index');
    }

    public function destroy()
    {
        if (!is_numeric(Request::data('studentId')) || !Student::exists('id', Request::data('studentId')))
        {
            Session::setFlash('An error occurred. Student is not valid.');
            Response::toAction('School#Teacher/Student#index');
        }

        $student = Student::find(Request::data('studentId'));
        if ($student->delete())
            Session::setFlash('Deleted student <b>' . $student->name() . '</b>.');
        else
            Session::setFlash('An error occurred. Could not delete student.');

        Response::toAction('School#Teacher/Student#index');
    }
}
<?php namespace Applications\School\Modules\Teacher\Student;

use Library\BackController;
use Library\Config;
use Library\Facades\Page;
use Library\Facades\Session;
use Library\Facades\Request;
use Library\Facades\Response;
use Models\ActivityStudent;
use Models\Address;
use Models\Student;
use Models\UserSetting;

class StudentController extends BackController
{
    public function index()
    {
        Page::add('students', $this->currentUser->students());
    }

    public function create()
    {
        Page::add('activities', $this->currentUser->activities());
    }

    public function store()
    {
        $generatedPassword = substr(md5(rand(999, 999999)), 0, 8);

        $student = Student::create([
            'teacher_id' => $this->currentUser->id,
            'school_id' => $this->currentUser->school()->id,
            'address_id' => Address::create()->id,
            'user_setting_id' => UserSetting::create()->id,
            'first_name' => Request::data('firstName'),
            'last_name' => Request::data('lastName'),
            'email' => Request::data('email'),
            'password' => md5($generatedPassword),
            'phone' => Request::data('phone'),
            'profile_picture' => Config::get('defaultProfilePicturePath')
        ]);

        if ($student == null)
        {
            Session::setFlash('An error occurred. Student was not added.');
            Response::toAction('School#Teacher/Student#index');
        }
        else
            Session::setFlash('Student <b>'.$student->name().'</b> was added successfully.');

        $activityStudent = ActivityStudent::create([
            'activity_id' => Request::data('activity'),
            'student_id' => $student->id
        ]);

        if ($activityStudent == null)
            Session::setFlash('An error occurred. Student was not added.');

        if (Request::data('createAnother') == 1)
            Response::toAction('School#Teacher/Student#create');

        Response::toAction('School#Teacher/Student#index');
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
            Session::setFlash('Deleted student <b>'.$student->name().'</b>.');
        else
            Session::setFlash('An error occurred. Could not delete student.');

        Response::toAction('School#Teacher/Student#index');
    }
}

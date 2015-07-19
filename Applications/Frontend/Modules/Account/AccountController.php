<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\DB;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\Address;
use Models\School;
use Models\Student;
use Models\Teacher;
use Models\TeacherSetting;
use Models\TempTeacher;
use Models\Subscription;

class AccountController extends BackController
{


    public function login()
    {
        $this->validateToken();
        $this->validateRequest([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $teacher = Teacher::where('email', '=', Request::data('email'))
            ->where('password', '=', md5(Request::data('password')))
            ->get()->first();

        if ($teacher != null)
        {
            Session::login($teacher->id, 'Teacher');
            Response::toAction('School#Teacher/Index#index');
        }

        $student = Student::where('email', '=', Request::data('email'))
            ->where('password', '=', md5(Request::data('password')))
            ->get()->first();

        if ($student != null)
        {
            Session::login($student->id, 'Student');
            Response::toAction('School#Student/Index#index');
        }

        Session::setFlash('The email or password is incorrect. Please try again.');
        Response::back();
    }



    public function resetPassword()
    {

    }

    public function signUp()
    {
        $memberships = [
            [
                'name' => 'Basic'
            ]
        ];
    }

    public function registerUser()
    {
        $this->validateToken();
        $this->validateRequest([
            'firstName' => ['required' => 'first name is required'],
            'lastName' => ['required' => 'last name is required'],
            'email' => ['email', 'unique:Teacher,email', 'unique:Student,email'],
            'subscriptionType' => 'required'
        ]);

        DB::beginTransaction();

        try
        {
            $subscription = Subscription::create([
                'type' => Request::data('subscriptionType')
            ]);

            $confirmationCode = md5(rand(999, 999999));

            TempTeacher::create([
                'subscription_id' => $subscription->id,
                'first_name' => Request::data('firstName'),
                'last_name' => Request::data('lastName'),
                'email' => Request::data('email'),
                'confirmation_code' => $confirmationCode
            ]);
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            Session::setFlash('An error occurred. Please try again.');
            Response::back();
        }

        DB::commit();
        Response::toAction('Frontend#Account#signUpLand');
    }

    public function signUpLand()
    {
        Page::add('confn', Session::getFlash()); // TEMP
    }

    public function emailConfirmation()
    {
        $tempTeacher = TempTeacher::find(Request::data('id'));
        if ($tempTeacher == null)
        {
            Session::setFlash('Your account no longer exists in our database');
            Response::toAction('Frontend#Account#signup');
        }

        if ($tempTeacher->confirmation_code !== Request::data('code'))
        {
            Session::setFlash('The confirmation code is invalid');
            Response::toAction('Frontend#Account#signup');
        }

        Page::add('tempTeacher', $tempTeacher);
    }

    public function completeRegistration()
    {
        $this->validateToken();
        $this->validateRequest([
            'password' => 'required',
            'confirmPassword' => ['required', 'equalsField:password' => 'passwords don\'t match']
        ]);

        $tempTeacher = TempTeacher::find(Request::data('tempTeacherId'));
        if ($tempTeacher == null)
        {
            Session::setFlash('Your account no longer exists. Please try signing up again.');
            Response::toAction('Fronend#Account#signup');
        }

        $subscription = Subscription::find($tempTeacher->subscription_id);
        if ($subscription == null)
        {
            Session::setFlash('An error occurred. Please try signing up again.');
            Response::toAction('Frontend#Account#signup');
        }

        DB::beginTransaction();

        try
        {
            $school = School::create([
                'name' => 'Your School',
                'address_id' => Address::create()->id
            ]);

            $teacher = Teacher::create([
                'subscription_id' => $subscription->id,
                'address_id' => Address::create()->id,
                'teacher_setting_id' => TeacherSetting::create()->id,
                'school_id' => $school->id,
                'first_name' => $tempTeacher->first_name,
                'last_name' => $tempTeacher->last_name,
                'email' => $tempTeacher->email,
                'password' => md5(Request::data('password')),
            ]);
        }
        catch (\PDOException $e)
        {
            DB::rollBack();
            Session::setFlash('An error occurred. Please try signing up again.');
            Response::toAction('Frontend#Account#signup');
        }

        DB::commit();

        $tempTeacher->delete();

        Session::login($teacher->id, 'Teacher');
        Response::toAction('School#Teacher/Index#index');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Library\Facades\DB;
use Library\Facades\Request;
use Library\Facades\Page;
use Library\Facades\Session;
use Library\Facades\Redirect;
use Models\Address;
use Models\School;
use Models\Student;
use Models\Teacher;
use Models\TeacherSetting;
use Models\TempTeacher;
use Models\Subscription;

class AccountController extends Controller
{
    public function index()
    {
        return view('frontend.account.index');
    }

    public function signup()
    {
        $memberships = array();

        $memberships[] = [
            'name' => 'Basic',
            'price' => 'FREE',
            'numStudents' => 5,
            'storageSpace' => '1GB'
        ];

        $memberships[] = [
            'name' => 'Silver',
            'price' => '14.99 / month',
            'numStudents' => 20,
            'storageSpace' => '5GB'
        ];

        $memberships[] = [
            'name' => 'Gold',
            'price' => '25.99 / month',
            'numStudents' => 50,
            'storageSpace' => '7GB'
        ];

        $memberships[] = [
            'name' => 'Platinum',
            'price' => '39.99 / month',
            'numStudents' => 'unlimited',
            'storageSpace' => '10GB'
        ];

        return view('frontend.account.signUp', compact('memberships'));
    }

    public function login()
    {
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
            Redirect::to('school.teacher.index.index');
            return;
        }

        $student = Student::where('email', '=', Request::data('email'))
            ->where('password', '=', md5(Request::data('password')))
            ->get()->first();

        if ($student != null)
        {
            Session::login($student->id, 'Student');
            Redirect::to('school.student.index.index');
            return;
        }

        Session::setFlash('The email or password is incorrect. Please try again.');
        Redirect::back();
        return;
    }

    public function logout()
    {
        Session::logout();
        Redirect::to('frontend.index.index');
    }

    public function resetPassword()
    {
        return view('frontend.account.resetPassword');
    }

    public function registerUser()
    {
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
        Redirect::to('frontend.account.signUpLand');
    }

    public function emailConfirmation()
    {
        $tempTeacher = TempTeacher::find(Request::data('id'));
        if ($tempTeacher == null)
        {
            Session::setFlash('Your account no longer exists in our database');
            Redirect::to('Frontend#Account#signup');
        }

        if ($tempTeacher->confirmation_code !== Request::data('code'))
        {
            Session::setFlash('The confirmation code is invalid');
            Redirect::to('frontend.account.signUp');
        }

        Page::add('tempTeacher', $tempTeacher);
    }

    public function completeRegistration()
    {
        $this->validateRequest([
            'password' => 'required',
            'confirmPassword' => ['required', 'equalsField:password' => 'passwords don\'t match']
        ]);

        $tempTeacher = TempTeacher::find(Request::data('tempTeacherId'));
        if ($tempTeacher == null)
        {
            Session::setFlash('Your account no longer exists. Please try signing up again.');
            Redirect::to('frontend.account.signUp');
        }

        $subscription = Subscription::find($tempTeacher->subscription_id);
        if ($subscription == null)
        {
            Session::setFlash('An error occurred. Please try signing up again.');
            Redirect::to('frontend.account.signUp');
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
            Redirect::to('frontend.account.signUp');
        }

        DB::commit();

        $tempTeacher->delete();

        Session::login($teacher->id, 'Teacher');
        Redirect::to('school.teacher.index.index');
    }

    public function signUpLand()
    {
        return view('frontend.account.signUpLand', ['confn' => Session::getFlash()]);
    }
}
<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Config;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\Address;
use Models\School;
use Models\Teacher;
use Models\TempTeacher;
use Models\UserInfo;
use Models\UserSetting;
use Models\Subscription;

class AccountController extends BackController
{
    public function index()
    {

    }

    public function login()
    {
        $userInfo = UserInfo::where('email', '=', Request::postData('email'))
            ->where('password', '=', md5(Request::postData('password')))
            ->get()->first();

        if ($userInfo == null)
        {
            Session::setErrors('The email or password is incorrect.');
            Response::back();
            return;
        }

        $teacher = $userInfo->teacher();
        if ($teacher != null)
        {
            Session::login($teacher->id, 'teacher');
            Response::toAction('School#Teacher/Index#index');
            return;
        }

        $student = $userInfo->student();
        if ($student != null)
        {
            Session::login($student->id, 'student');
            Response::toAction('School#Student/Index#index');
            return;
        }

        Session::setErrors('The email or password is incorrect.');
        Response::back();
    }

    public function logout()
    {
        Session::logout();
        Response::toAction('Frontend#Index#index');
    }

    public function resetPassword()
    {

    }

    public function signUp()
    {

    }

    public function registerUser()
    {
        if (UserInfo::exists('email', Request::postData('email')))
        {
            Session::setErrors(['Email is already in use.']);
            Response::back();
        }

        $subscription = Subscription::create(['type' => Request::postData('subscriptionType')]);
        $confirmationCode = md5(rand(999, 999999));

        $tempTeacher = new TempTeacher();
        $tempTeacher->subscription_id = $subscription->id;
        $tempTeacher->first_name = Request::postData('firstName');
        $tempTeacher->last_name = Request::postData('lastName');
        $tempTeacher->email = Request::postData('email');
        $tempTeacher->confirmation_code = $confirmationCode;
        $tempTeacher->save();

        Response::toAction('Frontend#Account#signUpLand');
    }

    public function signUpLand()
    {
        Page::add('confn', Session::getFlash()); // TEMP
    }

    public function emailConfirmation()
    {
        if (!TempTeacher::exists('id', Request::getData('id')))
        {
            Page::add('error', 'Your account no longer exists in our database.');
            return;   
        }

        $tempTeacher = TempTeacher::find(Request::getData('id'));

        if ($tempTeacher->confirmation_code !== Request::getData('code'))
        {
            Page::add('error', 'The confirmation code is invalid.');
            return;
        }

        Page::add('tempUser', $tempTeacher);
    }

    public function completeRegistration()
    {
        if (!TempTeacher::exists('id', Request::postData('tempUserId')))
        {
            Session::setErrors(['Your account no longer exists in our database.']);
            Response::back();
        }

        if (Request::postData('password') != Request::postData('confirmPassword'))
        {
            Session::setErrors(['Passwords don\'t match.']);
            Response::back();
        }

        $tempTeacher = TempTeacher::find(Request::postData('tempUserId'));

        $schoolAddress = Address::create();
        $school = School::create(['name' => 'Your School', 'address_id' => $schoolAddress->id]);
        $userAddress = Address::create();
        $userSetting = UserSetting::create();
        $subscription = Subscription::find($tempTeacher->subscription_id);

        $userInfo = new UserInfo();
        $userInfo->school_id = $school->id;
        $userInfo->address_id = $userAddress->id;
        $userInfo->user_setting_id = $userSetting->id;
        $userInfo->first_name = $tempTeacher->first_name;
        $userInfo->last_name = $tempTeacher->last_name;
        $userInfo->email = $tempTeacher->email;
        $userInfo->password = md5(Request::postData('password'));
        $userInfo->profile_picture = Config::get('defaultProfilePicturePath');
        $userInfo->save();

        $teacher = Teacher::create(['user_info_id' => $userInfo->id, 'subscription_id' => $subscription->id]);

        $teacher = UserInfo::where('email', '=', $userInfo->email)
            ->where('password', '=', $userInfo->password)
            ->get()->first();

        if ($teacher != null)
        {
            Session::login($teacher->id, 'teacher');
            Response::toAction('School#Teacher/Index#index');
        }
        else
            Response::toAction('Frontend#Account#index');
    }
}

<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\Address;
use Models\School;
use Models\Teacher;
use Models\UserInfo;
use Models\UserSetting;

class AccountController extends BackController
{
    public function index()
    {
        if (Session::hasErrors())
            Page::add('errors', Session::getErrors());
    }

    public function login()
    {
        $userInfo = UserInfo::where('email', '=', Request::postData('email'))
            ->where('password', '=', md5(Request::postData('password')))
            ->get();

        if ($userInfo != null)
        {
            Session::login($userInfo->id);
            Response::toAction('School#Index#index');
        }
        else
        {
            Session::setErrors('The email or password is incorrect.');
            Response::back();
        }
    }

    public function logout()
    {
        Session::logout();
        Response::toAction('Frontend#Index#index');
    }

    public function signUp()
    {
        if (Session::hasErrors())
            Page::add('errors', Session::getErrors());
    }

    public function registerUser()
    {
        if (UserInfo::exists('email', Request::postData('email')))
        {
            Session::setErrors(['Email is already in use.']);
            Response::back();
        }

        if (Request::postData('password') != Request::postData('confirmPassword'))
        {
            Session::setErrors(['Passwords don\'t match.']);
            Response::back();
        }

        $schoolAddress = Address::create();
        $school = School::create(['name' => 'Your School', 'address_id' => $schoolAddress->id]);
        $userAddress = Address::create();
        $userSetting = UserSetting::create();

        $userInfo = new UserInfo();
        $userInfo->school_id = $school->id;
        $userInfo->address_id = $userAddress->id;
        $userInfo->user_setting_id = $userSetting->id;
        $userInfo->first_name = Request::postData('firstName');
        $userInfo->last_name = Request::postData('lastName');
        $userInfo->email = Request::postData('email');
        $userInfo->password = md5(Request::postData('password'));
        $userInfo->save();

        Teacher::create(['user_info_id' => $userInfo->id, 'school_id' => $school->id]);

        Response::toAction('Frontend#Account#index');
    }
}

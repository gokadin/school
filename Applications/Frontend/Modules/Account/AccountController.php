<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\Address;
use Models\School;
use Models\Teacher;
use Models\User;
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
        $user = User::where('email', '=', Request::postData('email'))
            ->where('password', '=', md5(Request::postData('password')))
            ->get()->first();

        if ($user != null)
        {
            Session::login($user->id);
            Response::toAction('School#Teacher/Index#index');
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
        if (User::exists('email', Request::postData('email')))
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

        $user = new Teacher();
        $user->school_id = $school->id;
        $user->address_id = $userAddress->id;
        $user->user_setting_id = $userSetting->id;
        $user->first_name = Request::postData('firstName');
        $user->last_name = Request::postData('lastName');
        $user->email = Request::postData('email');
        $user->password = md5(Request::postData('password'));
        $user->save();

        Response::toAction('Frontend#Account#index');
    }
}

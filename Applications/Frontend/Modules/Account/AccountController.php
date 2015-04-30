<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\Address;
use Models\School;
use Models\Teacher;
use Models\TempUser;
use Models\User;
use Models\UserSetting;
use Models\Subscription;

class AccountController extends BackController
{
    public function index()
    {

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

    public function resetPassword()
    {

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

        $subscription = Subscription::create(['type' => Request::postData('subscriptionType')]);



        Response::toAction('Frontend#Account#signUpLand');
    }

    public function signUpLand()
    {

    }

    public function emailConfirmation()
    {
        $error = null;

        if (!TempUser::exists('id', Request::postData('id')))
            $error = 'Your account no longer exists in our database.';

        $tempUser = TempUser::find(Request::getData('id'));

        if ($tempUser->code !== Request::getData('code'))
            $error = 'The confirmation code is invalid.';

        if ($error != null)
            Page::add('error', $error);
        else
            Page::add('tempUser', $tempUser);
    }

    public function completeRegistration()
    {
        if (!TempUser::exists('id', Request::postData('tempUserId')))
        {
            Session::setErrors(['Your account no longer exists in our database.']);
            Response::back();
        }

        if (Request::postData('password') != Request::postData('confirmPassword'))
        {
            Session::setErrors(['Passwords don\'t match.']);
            Response::back();
        }

        $tempUser = TempUser::find(Request::postData('tempUserId'));

        $schoolAddress = Address::create();
        $school = School::create(['name' => 'Your School', 'address_id' => $schoolAddress->id]);
        $userAddress = Address::create();
        $userSetting = UserSetting::create();
        $subscription = Subscription::find($tempUser->subscription_id);

        $user = new Teacher();
        $user->school_id = $school->id;
        $user->subscription_id = $subscription->id;
        $user->address_id = $userAddress->id;
        $user->user_setting_id = $userSetting->id;
        $user->first_name = Request::postData('firstName');
        $user->last_name = Request::postData('lastName');
        $user->email = Request::postData('email');
        $user->password = md5(Request::postData('password'));
        $user->save();

        Response::toAction('Frontend#Account#login');
    }
}

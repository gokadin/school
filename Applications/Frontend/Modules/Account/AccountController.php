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
        $confirmationCode = md5(rand(999, 999999));

        $tempUser = new TempUser();
        $tempUser->subscription_id = $subscription->id;
        $tempUser->first_name = Request::postData('firstName');
        $tempUser->last_name = Request::postData('lastName');
        $tempUser->email = Request::postData('email');
        $tempUser->confirmation_code = $confirmationCode;
        $tempUser->save();

        Response::toAction('Frontend#Account#signUpLand');
    }

    public function signUpLand()
    {
        Page::add('confn', Session::getFlash()); // TEMP
    }

    public function emailConfirmation()
    {
        if (!TempUser::exists('id', Request::getData('id')))
        {
            Page::add('error', 'Your account no longer exists in our database.');
            return;   
        }

        $tempUser = TempUser::find(Request::getData('id'));

        if ($tempUser->confirmation_code !== Request::getData('code'))
        {
            Page::add('error', 'The confirmation code is invalid.');
            return;
        }

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

        $teacher = new Teacher();
        $teacher->school_id = $school->id;
        $teacher->subscription_id = $subscription->id;
        $teacher->address_id = $userAddress->id;
        $teacher->user_setting_id = $userSetting->id;
        $teacher->first_name = $tempUser->first_name;
        $teacher->last_name = $tempUser->last_name;
        $teacher->email = $tempUser->email;
        $teacher->password = md5(Request::postData('password'));
        $teacher->profile_picture = Config::get('defaultProfilePicturePath');
        $teacher->save();

        $user = User::where('email', '=', $teacher->email)
            ->where('password', '=', $teacher->password)
            ->get()->first();

        if ($user != null)
        {
            Session::login($user->id);
            Response::toAction('School#Teacher/Index#index');
        }
        else
            Response::toAction('Frontend#Account#index');
    }
}

<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Library\Facades\Config;
use Models\User;
use Models\School;

class AccountController extends BackController
{
    public function index()
    {
        $users = User::where('active', '>=', 0)->get();
        $schools = School::where('created_at', '>=', 0)->get();

        Page::add(['users' => $users, 'schools' => $schools]);
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

        $school = School::create(['name' => '']);

        $user = new User();
        $user->school_id = $school->id;
        $user->first_name = Request::postData('firstName');
        $user->last_name = Request::postData('lastName');
        $user->email = Request::postData('email');
        $user->password = md5(Request::postData('password'));
        $user->type = Config::get('testType');

        $user->save();

        Response::toAction('Frontend/Account/index');
    }
}

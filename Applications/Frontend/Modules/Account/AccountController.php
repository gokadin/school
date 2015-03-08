<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\DB;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;
use Models\User;
use Models\School;
use Carbon\Carbon;

class AccountController extends BackController
{
    public function index()
    {

    }

    public function signUp()
    {
        if (Session::hasErrors())
            Page::addVar('errors', Session::getErrors());
    }

    public function registerUser()
    {

    }
}

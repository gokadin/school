<?php namespace Applications\Frontend\Modules\Account;

use Library\BackController;
use Library\Facades\DB;
use Library\Facades\Request;
use Library\Facades\Response;
use Library\Facades\Page;
use Library\Facades\Session;

class AccountController extends BackController
{
    public function index()
    {
        $s = DB::table('school');

        $s->name = 'musamuse';
        $s->id = 3;

        echo $s->update();
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

<?php namespace Applications\Frontend\Modules\Ajax;

error_reporting(0);

use Library\BackController;
use Library\Facades\Request;
use Models\User;

class AjaxController extends BackController
{
    public function emailExists()
    {
        echo User::exists('email', Request::postData('email'));
    }
}
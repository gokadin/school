<?php
namespace Applications\Frontend\Modules\Account;
use Library\DB;
use Models\Users;

class AccountController extends \Library\BackController
{
    public function executeIndex()
    {
        $x = Users::select();
        echo sizeof($x);
    }

    public function executeSignUp()
    {
        echo $this->request()->getData('test');
    }

    public function executeRegisterUser()
    {
        $this->response()->redirect('/School/account/signup');
    }
}
?>
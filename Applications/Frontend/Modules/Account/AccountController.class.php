<?php
namespace Applications\Frontend\Modules\Account;
use \Library\DB as DB;

class AccountController extends \Library\BackController
{
    public function executeIndex()
    {
        
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
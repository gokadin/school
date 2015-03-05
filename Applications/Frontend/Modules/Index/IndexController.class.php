<?php
namespace Applications\Frontend\Modules\Index;

use Library\Facades\App;
use Library\Facades\Response;
use Library\Facades\DB;
use Library\Facades\Session;

class IndexController extends \Library\BackController
{
    public function index()
    {
        $errors = array('testkey' => 'testvalue');

        Response::toAction('Frontend/Account/index', $errors);
        //print_r(Session::getErrors());
    }
}
?>
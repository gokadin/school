<?php
namespace Applications\Frontend\Modules\Index;

use Library\Facades\App;
use Library\Facades\Response;
use Library\Facades\DB;
use Library\Facades\Session;

class IndexController extends \Library\BackController
{
    public function executeIndex()
    {
        $x = DB::table('users')->select();
        Session::set('x', 'xxx');
        echo Session::exists('x') ? 'true' : 'false';
    }
}
?>
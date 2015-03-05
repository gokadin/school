<?php
namespace Applications\Frontend\Modules\Index;

use Library\Facades\App;
use Library\Facades\Response;
use Library\Facades\DB;

class IndexController extends \Library\BackController
{
    public function executeIndex()
    {
        $x = DB::table('users')->select();
        echo sizeof($x);
    }
}
?>
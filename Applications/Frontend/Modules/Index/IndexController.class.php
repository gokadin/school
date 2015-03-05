<?php
namespace Applications\Frontend\Modules\Index;

use Library\Facades\App;
use Library\Facades\Response;
use Library\Facades\Users;

class IndexController extends \Library\BackController
{
    public function executeIndex()
    {
        $x = Users::select();
        echo sizeof($x);
    }
}
?>
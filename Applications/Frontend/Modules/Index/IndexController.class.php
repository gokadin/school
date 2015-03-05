<?php
namespace Applications\Frontend\Modules\Index;

use Library\Facades\App;

class IndexController extends \Library\BackController
{
    public function executeIndex()
    {
        echo App::name();
    }
}
?>
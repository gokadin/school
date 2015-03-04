<?php
namespace Applications\Frontend\Modules\Index;
use \Library\DB as DB;

class IndexController extends \Library\BackController
{
    public function executeIndex()
    {
        $users = DB::table('users')->select();

        $this->page->addVar('users', $users);
    }
}
?>
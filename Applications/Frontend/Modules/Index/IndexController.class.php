<?php
namespace Applications\Frontend\Modules\Index;
use \Library\DB as DB;
use \Library\HTTPRequest as HTTPRequest;

class IndexController extends \Library\BackController {
    public function executeIndex(HTTPRequest $request)
    {
        $users = DB::table('users')->select();

        $this->page->addVar('users', $users);
    }

    public function executeInsert(HTTPRequest $request)
    {
        $this->app->httpResponse()->redirect('www.google.com');
    }
}
?>
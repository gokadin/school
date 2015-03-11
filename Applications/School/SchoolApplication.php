<?php
namespace Applications\School;

use Library\Application;
use Library\Facades\Session;
use Library\Facades\Response;
use Models\UserInfo;
use Models\Teacher;

class SchoolApplication extends Application
{
    public function __construct()
    {
        parent::__construct($this);
        
        $this->name = 'School';
    }
    
    public function run()
    {
        $controller = $this->getController();

        // SETTING RESTRICTIONS

        // SETTING NON DEFAULT LAYOUTS

        $currentUser = Teacher::where('user_info_id', '=', UserInfo::find(Session::get('id'))->id)->get()->first();
        if ($currentUser == null || !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $controller->page()->add(['currentUser' => $currentUser]);
        
        $controller->execute();
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}

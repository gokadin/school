<?php
namespace Applications\School;

use Library\Application;
use Library\Facades\Session;
use Library\Facades\Response;
use Models\User;

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

        $currentUser = User::find(Session::get('id'));
        $currentUser = $currentUser->morph();
        if ($currentUser == null || !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $controller->page()->add(['currentUser' => $currentUser]);
        
        $controller->execute();
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}

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

        $currentUser = User::find(Session::get('id'));
        $currentUser = $currentUser->morph();

        if ($currentUser == null || !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $controller->page()->add(['currentUser' => $currentUser]);

        if ($currentUser->meta_type == 'Teacher')
        {
            if (substr($controller->module(), 0, 7) == 'Student/')
                Response::toAction('School#Teacher/Index#index');
        }
        else if ($currentUser->meta_type == 'Student')
        {
            if (substr($controller->module(), 0, 7) == 'Teacher/')
                Response::toAction('School#Student/Index#index');
        }
        else
        {
            Response::toAction('Frontend#Account#index');
        }
        
        $controller->execute();
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}

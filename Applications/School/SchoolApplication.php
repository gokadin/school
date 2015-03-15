<?php namespace Applications\School;

use Library\Application;
use Library\Facades\Session;
use Library\Facades\Response;
use Library\Facades\Router;
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

        /* ASSIGNING CURRENT USER */

        $currentUser = User::find(Session::get('id'));
        $currentUser = $currentUser->morph();

        /* PERMISSIONS */

        if ($currentUser == null || !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $controller->add(['currentUser' => $currentUser]);
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

        /* BREADCRUMBS */

        $breadcrumbs = $this->buildBreadcrumbs($controller, $currentUser->meta_type);
        $controller->page()->add(['breadcrumbs' => $breadcrumbs]);
        $controller->page()->add(['module' => $controller->module(), 'action' => $controller->action()]);
        
        $controller->execute();
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }

    private function buildBreadcrumbs($controller, $userType)
    {
        $breadcrumbs = array();

        if ($userType == 'Teacher')
            $breadcrumbs['Home'] = Router::actionToPath('School#Teacher/Index#index');
        else
            $breadcrumbs['Home'] = Router::actionToPath('School#Student/Index#index');

        switch ($controller->module())
        {
            case 'Teacher/Activity':
                $breadcrumbs['Activities'] = Router::actionToPath('School#Teacher/Activity#index');
                break;
            case 'Teacher/Student':
                $breadcrumbs['Students'] = Router::actionToPath('School#Teacher/Student#index');
                break;
        }

        return $breadcrumbs;
    }
}

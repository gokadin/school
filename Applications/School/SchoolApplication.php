<?php namespace Applications\School;

use Library\Application;
use Library\Facades\Session;
use Library\Facades\Response;
use Library\Facades\Router;
use Models\User;

class SchoolApplication extends Application
{
    public function run()
    {
        $controller = $this->getController();

        /* ASSIGNING CURRENT USER */

        if (!Session::exists('id'))
            Response::toAction('Frontend#Account#index');

        $currentUser = User::find(Session::get('id'));
        $currentUser = $currentUser->morph();

        /* PERMISSIONS */

        if ($currentUser == null || !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $controller->add(['currentUser' => $currentUser]);
        \Library\Facades\Page::add(['currentUser' => $currentUser]);

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
        \Library\Facades\Page::add(['breadcrumbs' => $breadcrumbs]);
        \Library\Facades\Page::add(['module' => $controller->module(), 'action' => $controller->action()]);
        
        $controller->execute();
        Response::send();
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

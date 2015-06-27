<?php namespace Applications\School;

use Library\Application;
use Library\Facades\Page;
use Library\Facades\Session;
use Library\Facades\Response;
use Library\Facades\Router;
use Models\Student;
use Models\Teacher;

class SchoolApplication extends Application
{
    public function run()
    {
        $this->processRoute();
        $controller = $this->getController();

        /* ASSIGNING CURRENT USER */

        if (!Session::exists('id') || !Session::exists('type')|| !Session::get('authenticated'))
            Response::toAction('Frontend#Account#index');

        $currentUser = null;
        if (Session::get('type') == 'Teacher')
            $currentUser = Teacher::find(Session::get('id'));
        else if (Session::get('type') == 'Student')
            $currentUser = Student::find(Session::get('id'));

        if ($currentUser == null)
        {
            Session::setFlash('Please login again.');
            Response::toAction('Frontend#Account#index');
        }

        $controller->add(['currentUser' => $currentUser]);
        Page::add(['currentUser' => $currentUser]);

        /* PERMISSIONS */

        if (Session::get('type') == 'Teacher')
        {
            if (substr($this->module(), 0, 7) == 'Student/')
                Response::toAction('School#Teacher/Index#index');
        }
        else if (Session::get('type') == 'Student')
        {
            if (substr($this->module(), 0, 7) == 'Teacher/')
                Response::toAction('School#Student/Index#index');
        }

        /* BREADCRUMBS */

        $breadcrumbs = $this->buildBreadcrumbs();
        Page::add(['breadcrumbs' => $breadcrumbs]);
        Page::add(['module' => $this->module(), 'action' => $this->action()]);


        require '/Web/lang/common.php';
        $controller->setLang($lang);
        Page::add('lang', $lang);

        $controller->execute();
        Response::send();
    }

    private function buildBreadcrumbs()
    {
        $breadcrumbs = array();

        if (Session::get('type') == 'teacher')
            $breadcrumbs['Home'] = Router::actionToPath('School#Teacher/Index#index');
        else
            $breadcrumbs['Home'] = Router::actionToPath('School#Student/Index#index');

        switch ($this->module())
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

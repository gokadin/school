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

        $currentTeacher = null;
        $currentStudent = null;
        if (Session::get('type') == 'teacher')
            $currentTeacher = Teacher::find(Session::get('id'));
        else if (Session::get('type') == 'student')
            $currentStudent = Student::find(Session::get('id'));

        /* PERMISSIONS */

        if ($currentTeacher == null && $currentStudent == null)
            Response::toAction('Frontend#Account#index');

        $currentUser = $currentTeacher == null ? $currentStudent : $currentTeacher;

        $controller->add(['currentUser' => $currentUser]);
        Page::add(['currentUser' => $currentUser]);

        if ($currentStudent == null)
        {
            if (substr($this->module(), 0, 7) == 'Student/')
                Response::toAction('School#Teacher/Index#index');
        }
        else if ($currentTeacher == null)
        {
            if (substr($this->module(), 0, 7) == 'Teacher/')
                Response::toAction('School#Student/Index#index');
        }
        else
        {
            Response::toAction('Frontend#Account#index');
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

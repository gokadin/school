<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Library\Routing\Router;
use Library\Facades\Sentry;
use Library\Http\Request;
use Closure;
use Library\Http\View;

class VerifyAuthentication
{
    protected $userRepository;
    protected $view;
    protected $redirect;
    protected $router;

    public function __construct(UserRepository $userRepository, View $view, Redirect $redirect, Router $router)
    {
        $this->userRepository = $userRepository;
        $this->view = $view;
        $this->redirect = $redirect;
        $this->router = $router;
    }

    public function handle(Request $request, Closure $next)
    {// 1: CHANGE USER REPO FOR LOGIN HANDLER OR SOMETHING
        // 2: MAKE THE CONSTRUCTOR RESOLVABLE
        if (!$this->userRepository->loggedIn())
        {
            $this->redirect->to('frontend.account.login');
            return;
        }

        if ($this->router->currentNameContains('school.teacher.') && $this->userRepository->type() != 'Teacher')
        {
            $this->redirect->to('frontend.account.login');
        }

        if ($this->router->currentNameContains('school.student.') && $this->userRepository->type() != 'Student')
        {
            $this->redirect->to('frontend.account.login');
        }

        $this->view->add(['user' => $this->userRepository->getLoggedInUser()]);

        return $next($request);
    }
}
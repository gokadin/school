<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Routing\Router;
use Library\Http\Request;
use Closure;
use Library\Http\View;

class VerifyAuthentication
{
    protected $userRepository;
    protected $view;
    protected $router;
    protected $response;

    public function __construct(UserRepository $userRepository, View $view, Router $router, Response $response)
    {
        $this->userRepository = $userRepository;
        $this->view = $view;
        $this->router = $router;
        $this->response = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->userRepository->loggedIn())
        {
            if ($request->isJson())
            {
                return $this->response->json([], 401);
            }

            $this->response->route('frontend.account.login');
            return;
        }

        $this->view->add(['user' => $this->userRepository->getLoggedInUser()]);

        if ($this->router->currentNameContains('api.'))
        {
            return $next($request);
        }

        if ($this->router->currentNameContains('school.teacher.') && $this->userRepository->getLoggedInType() != 'teacher')
        {
            $this->response->route('frontend.account.login');
        }

        if ($this->router->currentNameContains('school.student.') && $this->userRepository->getLoggedInType() != 'student')
        {
            $this->response->route('frontend.account.login');
        }

        return $next($request);
    }
}
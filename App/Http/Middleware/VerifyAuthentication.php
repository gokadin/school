<?php

namespace App\Http\Middleware;

use App\Domain\Users\Authenticator;
use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Routing\Router;
use Library\Http\Request;
use Closure;
use Library\Http\View;

class VerifyAuthentication
{
    protected $authenticator;
    protected $view;
    protected $router;
    protected $response;

    public function __construct(Authenticator $authenticator, View $view, Router $router, Response $response)
    {
        $this->authenticator = $authenticator;
        $this->view = $view;
        $this->router = $router;
        $this->response = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$request->isJson() || is_null($request->header('Authorization')) ||
            !$this->authenticator->processAuthorization($request->header('Authorization')))
        {
            $this->response->json([], 401);
            $this->response->executeResponse();
            return;
        }

        if ($this->router->currentNameContains('school.teacher') && $this->authenticator->type() != 'teacher' ||
            $this->router->currentNameContains('school.student') && $this->authenticator->type() != 'student')
        {
            $this->response->json([], 401);
            $this->response->executeResponse();
            return;
        }

        return $next($request);
    }
}
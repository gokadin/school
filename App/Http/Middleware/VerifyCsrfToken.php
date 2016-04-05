<?php

namespace App\Http\Middleware;

use Closure;
use Library\Http\Response;
use Library\Session\Session;
use Library\Http\Request;
use Symfony\Component\Yaml\Exception\RuntimeException;

class VerifyCsrfToken
{
    protected $methodsToVerify = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    protected $session;
    protected $response;

    public function __construct(Session $session, Response $response)
    {
        $this->session = $session;
        $this->response = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->method(), $this->methodsToVerify))
        {
            return $next($request);
        }

        $token = $this->session->generateToken();
        if ($request->isJson() && $request->header('CSRFTOKEN') != $token)
        {
            $this->response->json([], 401);
            $this->response->executeResponse();
            return;
        }

        if (!$request->isJson() && $request->data('_token') != $token)
        {
            throw new RuntimeException('CSRF token mismatch.');
            return;
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Library\Http\Response;
use Library\Session\Session;
use Library\Http\Request;
use Symfony\Component\Yaml\Exception\RuntimeException;

class VerifyCsrfToken
{
    protected $methodsToVerify = ['POST', 'PUT', 'PATCH', 'DELETE'];
    protected $session;
    protected $response;

    public function __construct(Session $session, Response $response)
    {
        $this->session = $session;
        $this->response = $response;
    }

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), $this->methodsToVerify))
        {
            $token = $this->session->generateToken();
            if ($request->data('_token') != $token && $request->header('HTTP_CSRFTOKEN') != $token)
            {
                if ($request->isJson())
                {
                    return $this->response->json([print_r($_SERVER)], 401);
                }

                throw new RuntimeException('CSRF token mismatch.');
            }
        }

        return $next($request);
    }
}
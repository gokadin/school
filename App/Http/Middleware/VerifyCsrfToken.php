<?php

namespace App\Http\Middleware;

use Closure;
use Library\Session\Session;
use Library\Http\Request;
use Symfony\Component\Yaml\Exception\RuntimeException;

class VerifyCsrfToken
{
    protected $methodsToVerify = ['POST', 'PUT', 'PATCH', 'DELETE'];
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), $this->methodsToVerify))
        {
            $token = $this->session->generateToken();
            if ($request->data('_token') != $token && $request->header('HTTP_CSRF_TOKEN') != $token)
            {
                throw new RuntimeException('CSRF token mismatch.');
            }
        }

        return $next($request);
    }
}
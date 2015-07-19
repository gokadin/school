<?php

namespace App\Http\Middleware;

use Closure;
use Library\Facades\Session;
use Library\Http\Request;

class VerifyCsrfToken
{
    protected $methodsToVerify = [
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), $this->methodsToVerify))
        {
            $token = Session::generateToken();
            if ($request::data('_token') != $token && $request::header('HTTP_CSRF_TOKEN') != $token)
            {
                throw new RuntimeException('CSRF token mismatch.');
            }
        }

        return $next($request);
    }
}
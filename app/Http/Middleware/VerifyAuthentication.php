<?php

namespace App\Http\Middleware;

use Library\Http\Request;
use Closure;

class VerifyAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        // ...

        return $next($request);
    }
}
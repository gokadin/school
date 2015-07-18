<?php

namespace App\Http\Middleware;

use Closure;
use Library\Request;

class VerifyCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        echo 'heello';
        return $next($request);
    }
}
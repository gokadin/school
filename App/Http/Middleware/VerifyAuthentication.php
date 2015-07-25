<?php

namespace App\Http\Middleware;

use Library\Facades\Redirect;
use Library\Facades\Router;
use Library\Facades\Sentry;
use Closure;

class VerifyAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!Sentry::loggedIn())
        {
            Redirect::to('frontend.account.login');
            return;
        }

        if (Router::currentNameContains('school.teacher.') && Sentry::type() != 'Teacher')
        {
            Redirect::to('frontend.account.login');
        }

        if (Router::currentNameContains('school.student.') && Sentry::type() != 'Student')
        {
            Redirect::to('frontend.account.login');
        }

        return $next($request);
    }
}
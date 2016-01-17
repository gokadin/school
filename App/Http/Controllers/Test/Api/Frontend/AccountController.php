<?php

namespace App\Http\Controllers\Test\Api\Frontend;

use App\Domain\Services\LoginService;
use App\Http\Controllers\Test\Api\ApiController;
use App\Http\Requests\Test\Frontend\LoginRequest;

class AccountController extends ApiController
{
    public function login(LoginRequest $loginRequest, LoginService $loginService)
    {
        $authToken = $loginService->login($loginRequest->data('email'), $loginRequest->data('password'));

        return !$authToken
            ? $this->respondBadRequest()
            : $this->respondOk(['authToken' => $authToken]);
    }
}
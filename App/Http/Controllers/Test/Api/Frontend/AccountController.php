<?php

namespace App\Http\Controllers\Test\Api\Frontend;

use App\Http\Controllers\Test\Api\ApiController;
use App\Http\Requests\Test\Frontend\LoginRequest;

class AccountController extends ApiController
{
    public function login(LoginRequest $loginRequest)
    {
        return $this->respondOk();
    }
}
<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Frontend\Account\CurrentUserRequest;
use App\Http\Requests\Api\Frontend\Account\LoginRequest;
use App\Http\Translators\Api\Frontend\Account\CurrentUserTranslator;
use App\Http\Translators\Api\Frontend\Account\LoginTranslator;

class AccountController extends ApiController
{
    public function currentUser(CurrentUserRequest $request, CurrentUserTranslator $translator)
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function login(LoginRequest $request, LoginTranslator $translator)
    {
        return $this->respond($translator->translateRequest($request));
    }
}
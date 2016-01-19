<?php

namespace App\Http\Controllers\Api\School;

use App\Domain\Services\UserService;
use App\Http\Controllers\Api\ApiController;

class SchoolController extends ApiController
{
    public function currentUser(UserService $userService)
    {
        return $this->respondOk($userService->currentUser());
    }
}
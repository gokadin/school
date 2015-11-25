<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class ActivityController extends Controller
{
    public function userActivities(UserRepository $userRepository)
    {
        return $userRepository->getLoggedInUser()->activities();
    }
}
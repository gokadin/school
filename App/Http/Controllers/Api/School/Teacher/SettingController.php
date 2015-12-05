<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class SettingController extends Controller
{
    public function getRegistration(UserRepository $userRepository)
    {
        return $userRepository->getLoggedInUser()->settings()->registrationForm();
    }
}
<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\School\GetUserActivitiesRequest;
use App\Repositories\UserRepository;

class ActivityController extends Controller
{
    public function userActivities(UserRepository $userRepository)
    {
        return $userRepository->getLoggedInUser()->activities();
    }

    public function getTeacherActivities(GetTeacherActivitiesRequest $request, UserRepository $userRepository)
    {
        $activities = $userRepository->getLoggedInUser()->activities()
            ->sortBy($request->sortBy, $request->ascending)
            ->paginate($request->currentPage, $request->max);

        foreach ($request->filters as $property => $string)
        {
            
        }

        // return something
    }
}
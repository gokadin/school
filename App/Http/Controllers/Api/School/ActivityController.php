<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\School\GetTeacherActivitiesRequest;
use App\Repositories\UserRepository;

class ActivityController extends Controller
{
    public function getTeacherActivities(GetTeacherActivitiesRequest $request, UserRepository $userRepository)
    {
        $activities = $userRepository->getLoggedInUser()->activities();

        foreach ($request->sortingRules as $property => $ascending)
        {
            switch ($ascending)
            {
                case 'asc':
                    $activities->sortBy($property, true);
                    break;
                case 'desc':
                    $activities->sortBy($property, false);
                    break;
            }
        }

        foreach ($request->filters as $property => $string)
        {
            $activities->where($property, 'LIKE', '%'.$string.'%');
        }

        $totalCount = $activities->count();

        return [
            'activities' => $activities->slice($request->page * $request->max, $request->max),
            'totalCount' => $totalCount
        ];
    }
}
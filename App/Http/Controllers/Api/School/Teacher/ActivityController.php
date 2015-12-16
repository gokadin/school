<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\GetTeacherActivitiesRequest;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;

class ActivityController extends ApiController
{
    public function index(GetTeacherActivitiesRequest $request, ActivityRepository $activityRepository)
    {
        $sortingRules = $request->dataExists('sortingRules') ? $request->sortingRules : [];
        $searchRules = $request->dataExists('searchRules') ? $request->searchRules : [];

        return $this->respondOk($activityRepository->paginate(
            $request->page, $request->max > 20 ? 20 : $request->max, $sortingRules, $searchRules));
    }

    public function destroy(UserRepository $userRepository, ActivityRepository $activityRepository, $activityId)
    {
        $activity = $userRepository->getLoggedInUser()->activities()->find($activityId);

        if (is_null($activity))
        {
            return $this->respondUnauthorized();
        }

        $activityRepository->delete($activity);

        return $this->respondOk();
    }
}
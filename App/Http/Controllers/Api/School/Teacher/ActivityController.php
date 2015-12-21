<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\GetTeacherActivitiesRequest;
use App\Http\Requests\Api\School\UpdateActivityRequest;
use App\Repositories\ActivityRepository;
use App\Repositories\UserRepository;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class ActivityController extends ApiController
{
    /**
     * @var ActivityService
     */
    private $activityService;

    public function __construct(View $view, Session $session, Response $response, ActivityService $activityService)
    {
        parent::__construct($view, $session, $response);

        $this->activityService = $activityService;
    }

    public function index(GetTeacherActivitiesRequest $request)
    {
        return $this->respondOk($this->activityService->getActivityList($request->all()));
    }

    public function students($id)
    {
        $students = $this->activityService->getActivityStudentList($id);
        if (!$students)
        {
            $this->respondBadRequest();
        }

        return $this->respondOk(['students' => $students]);
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

    public function update(UpdateActivityRequest $request, UserRepository $userRepository, ActivityRepository $activityRepository, $activityId)
    {
        $activity = $userRepository->getLoggedInUser()->activities()->find($activityId);

        if (is_null($activity))
        {
            return $this->respondUnauthorized();
        }

        $activityRepository->update($activity, $request->all());

        return $this->respondOk();
    }
}
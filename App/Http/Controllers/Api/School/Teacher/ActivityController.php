<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\GetTeacherActivitiesRequest;
use App\Http\Requests\Api\School\UpdateActivityRequest;

class ActivityController extends ApiController
{
    public function index(GetTeacherActivitiesRequest $request, ActivityService $activityService)
    {
        return $this->respondOk($activityService->getActivityList($request->all()));
    }

    public function getAll(ActivityService $activityService)
    {
        return $this->respondOk(['activities' => $activityService->getActivities()]);
    }

    public function students(ActivityService $activityService, $id)
    {
        $students = $activityService->getActivityStudentList($id);

        return !$students
            ? $this->respondBadRequest()
            : $this->respondOk(['students' => $students]);
    }

    public function destroy(ActivityService $activityService, $activityId)
    {
        return !$activityService->delete($activityId)
            ? $this->respondUnauthorized()
            : $this->respondOk();
    }

    public function update(UpdateActivityRequest $request, ActivityService $activityService, $activityId)
    {
        return !$activityService->update($request->all(), $request->get('activityId'))
            ? $this->respondUnauthorized()
            : $this->respondOk();
    }
}
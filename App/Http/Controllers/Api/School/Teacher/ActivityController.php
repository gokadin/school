<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Activity\PaginateRequest;
use App\Http\Requests\Api\School\Teacher\Activity\StoreRequest;
use App\Http\Translators\Api\School\Teacher\Activity\PaginateTranslator;
use Library\Http\Response;

class ActivityController extends ApiController
{
//    public function index(GetTeacherActivitiesRequest $request, ActivityService $activityService)
//    {
//        return $this->respondOk($activityService->getActivityList($request->all()));
//    }

    public function paginate(PaginateRequest $request, PaginateTranslator $translator): Response
    {
        $data = $translator->translateRequest($request);

        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }

    public function store(StoreRequest $request, ActivityService $activityService) : Response
    {
        return $this->respondOk();
        return $activityService->create($request->all()) ? $this->respondOk() : $this->respondBadRequest();
    }

//    public function getAll(ActivityService $activityService)
//    {
//        return $this->respondOk(['activities' => $activityService->getActivities()]);
//    }
//
//    public function students(ActivityService $activityService, $id)
//    {
//        $students = $activityService->getActivityStudentList($id);
//
//        return !$students
//            ? $this->respondBadRequest()
//            : $this->respondOk(['students' => $students]);
//    }
//
//    public function destroy(ActivityService $activityService, $activityId)
//    {
//        return !$activityService->delete($activityId)
//            ? $this->respondUnauthorized()
//            : $this->respondOk();
//    }
//
//    public function update(UpdateActivityRequest $request, ActivityService $activityService, $activityId)
//    {
//        return !$activityService->update($request->all(), $request->get('activityId'))
//            ? $this->respondUnauthorized()
//            : $this->respondOk();
//    }
}
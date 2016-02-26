<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\School\Teacher\Activity\PaginateRequest;
use App\Http\Requests\Api\School\Teacher\Activity\StoreRequest;
use App\Http\Requests\Api\School\Teacher\Activity\StudentsRequest;
use App\Http\Translators\Api\School\Teacher\Activity\PaginateTranslator;
use App\Http\Translators\Api\School\Teacher\Activity\StudentsTranslator;
use Library\Http\Response;

class ActivityController extends ApiController
{
    public function paginate(PaginateRequest $request, PaginateTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function students(StudentsRequest $request, StudentsTranslator $translator): Response
    {
        return $this->respond($translator->translateRequest($request));
    }

    public function store(StoreRequest $request, ActivityService $activityService) : Response
    {
        // needs refactoring
        return $this->respondOk();
        return $activityService->create($request->all()) ? $this->respondOk() : $this->respondBadRequest();
    }
}
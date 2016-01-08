<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreActivityRequest;
use App\Jobs\School\CreateActivity;

class ActivityController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.activity.index');
    }

    public function create()
    {
        return $this->view->make('school.teacher.activity.create');
    }

    public function store(StoreActivityRequest $request, ActivityService $activityService)
    {
        $activityService->create($request->all());

        return $request->createAnother == 1
            ? $this->response->route('school.teacher.activity.create')->withFlash('Activity created!')
            : $this->response->route('school.teacher.activity.index')->withFlash('Activity created!');
    }
}
<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreActivityRequest;
use App\Jobs\School\CreateActivity;
use App\Repositories\UserRepository;

class ActivityController extends Controller
{
    public function index(UserRepository $userRepository)
    {
        $activities = $userRepository->getLoggedInUser()->activities();

        return $this->view->make('school.teacher.activity.index', compact('activities'));
    }

    public function create()
    {
        return $this->view->make('school.teacher.activity.create');
    }

    public function store(StoreActivityRequest $request)
    {
        $this->dispatchJob(new CreateActivity($request->all()));

        $request->createAnother == 1
            ? $this->response->route('teacher.account.activity.create')
            : $this->response->route('teacher.account.activity.index');
    }
}
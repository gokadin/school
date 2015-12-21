<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\ActivityService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreActivityRequest;
use App\Jobs\School\CreateActivity;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class ActivityController extends Controller
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

    public function index()
    {
        return $this->view->make('school.teacher.activity.index');
    }

    public function create()
    {
        return $this->view->make('school.teacher.activity.create');
    }

    public function store(StoreActivityRequest $request)
    {
        $this->activityService->create($request->all());

        $this->session->setFlash('Activity created!');

        $request->createAnother == 1
            ? $this->response->route('teacher.account.activity.create')
            : $this->response->route('teacher.account.activity.index');
    }
}
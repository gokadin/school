<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\PreRegisterStudentRequest;
use App\Jobs\School\PreRegisterStudent;
use App\Repositories\UserRepository;

class StudentController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.student.index');
    }

    public function create(UserRepository $userRepository)
    {
        return $this->view->make('school.teacher.student.create', [
            'activities' => $userRepository->getLoggedInUser()->activities()
        ]);
    }

    public function preRegister(PreRegisterStudentRequest $request)
    {
        $this->dispatchJob(new PreRegisterStudent($request->all()));

        $request->createAnother == 1
            ? $this->response->route('school.teacher.student.create')
            : $this->response->route('school.teacher.student.index');
    }
}
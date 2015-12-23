<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\StudentRegistrationService;
use App\Domain\Services\StudentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\PreRegisterStudentRequest;
use App\Http\Requests\School\ShowStudentRequest;

class StudentController extends Controller
{
    public function index()
    {
        return $this->view->make('school.teacher.student.index');
    }

    public function create(StudentRegistrationService $studentRegistrationService)
    {
        return $this->view->make('school.teacher.student.create',
            $studentRegistrationService->preparePreRegistrationData());
    }

    public function preRegister(PreRegisterStudentRequest $request, StudentService $studentService)
    {
        if (!$studentService->preRegister($request->all()))
        {
            $this->session->setFlash('There was an error while registering your student. please try again.', 'error');
            $this->response->route('school.teacher.student.create');
        }

        $this->session->setFlash('Invitation sent!');
        $request->createAnother == 1
            ? $this->response->route('school.teacher.student.create')
            : $this->response->route('school.teacher.student.index');
    }

    public function show(ShowStudentRequest $request, StudentService $studentService)
    {
        return $this->view->make('school.teacher.student.show',
            $studentService->getProfile($request->id));
    }
}
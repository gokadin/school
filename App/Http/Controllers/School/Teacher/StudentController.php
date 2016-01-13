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
            return $this->response->route('school.teacher.student.create')
                ->withFlash('There was an error while registering your student. please try again.', 'error');
        }

        return $request->createAnother == 1
            ? $this->response->route('school.teacher.student.create')->withFlash('Invitation sent!')
            : $this->response->route('school.teacher.student.index')->withFlash('Invitation sent!');
    }

    public function show(ShowStudentRequest $request, StudentService $studentService)
    {
        return $this->view->make('school.teacher.student.show',
            $studentService->getProfile($request->id));
    }

    public function lessons(ShowStudentRequest $request, StudentService $studentService)
    {
        return $this->view->make('school.teacher.student.lessons', [
            'student' => $studentService->findStudent($request->id)
        ]);
    }
}
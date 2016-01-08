<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Services\StudentRegistrationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\RegisterStudentRequest;

class StudentController extends Controller
{
    public function index(StudentRegistrationService $studentRegistrationService, $id, $code)
    {
        $tempStudent = $studentRegistrationService->validateTempStudent($id, $code);
        if (!$tempStudent)
        {
            return $this->response->route('frontend.student.notFound');
        }

        return $this->view->make('frontend.student.index',
            $studentRegistrationService->prepareRegistrationData($tempStudent));
    }

    public function notFound()
    {
        return $this->view->make('frontend.student.notFound');
    }

    public function register(RegisterStudentRequest $request, StudentRegistrationService $studentRegistrationService)
    {
        $studentRegistrationService->register($request->all());

        return $this->response->route('frontend.account.login');
    }
}
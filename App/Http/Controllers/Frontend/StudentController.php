<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Services\StudentRegistrationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\RegisterStudentRequest;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class StudentController extends Controller
{
    public function index(StudentRegistrationService $studentRegistrationService, $id, $code)
    {
        $tempStudent = $studentRegistrationService->validateTempStudent($id, $code);
        if (!$tempStudent)
        {
            $this->response->route('frontend.student.notFound');
        }

        return $this->view->make('frontend.student.index',
            $studentRegistrationService->prepareRegistrationData($tempStudent));
    }

    public function notFound()
    {
        return $this->view->make('frontend.student.notFound');
    }

    public function register(RegisterStudentRequest $request)
    {
        $this->studentRegistrationService->register($request->all());

        //$this->response->route('frontend.account.login');
    }
}
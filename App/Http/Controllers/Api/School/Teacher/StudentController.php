<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\StudentService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\GetTeacherStudentsRequest;

class StudentController extends ApiController
{
    public function index(GetTeacherStudentsRequest $request, StudentService $studentService)
    {
        return $this->respondOk($studentService->getStudentList($request->all()));
    }
}
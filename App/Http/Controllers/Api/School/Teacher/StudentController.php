<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\StudentService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\FromStudentIdsRequest;
use App\Http\Requests\Api\School\GetTeacherStudentsRequest;
use App\Http\Requests\Api\School\SearchStudentRequest;
use App\Http\Requests\Api\School\StudentLessonsRequest;

class StudentController extends ApiController
{
    public function index(GetTeacherStudentsRequest $request, StudentService $studentService)
    {
        return $this->respondOk($studentService->getStudentList($request->all()));
    }

    public function fromIds(FromStudentIdsRequest $request, StudentService $studentService)
    {
        return $this->respondOk(['students' => $studentService->getInIds($request->ids)]);
    }

    public function search(SearchStudentRequest $request, StudentService $studentService)
    {
        return $this->respondOk(['results' => $studentService->search($request->all())]);
    }

    public function newStudents(StudentService $studentService)
    {
        return $this->respondOk(['newStudents' => $studentService->newStudents()]);
    }

    public function lessons(StudentLessonsRequest $request, StudentService $studentService)
    {
        return $studentService->getLessons($request->get('id'), $request->all());
    }
}
<?php

namespace App\Http\Controllers\Api\School\Teacher;

use App\Domain\Services\StudentService;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\School\GetTeacherStudentsRequest;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class StudentController extends ApiController
{
    /**
     * @var StudentService
     */
    private $studentService;

    public function __construct(View $view, Session $session, Response $response, StudentService $studentService)
    {
        parent::__construct($view, $session, $response);

        $this->studentService = $studentService;
    }

    public function index(GetTeacherStudentsRequest $request)
    {
        return $this->respondOk($this->studentService->getStudentList($request->all()));
    }
}
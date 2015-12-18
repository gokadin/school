<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\StudentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\PreRegisterStudentRequest;
use App\Http\Requests\School\ShowStudentRequest;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class StudentController extends Controller
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
        if (!$this->studentService->preRegister($request->all()))
        {
            $this->session->setFlash('There was an error while registering your student. please try again.', 'error');
            $this->response->route('school.teacher.student.create');
        }

        $this->session->setFlash('Invitation sent!');
        $request->createAnother == 1
            ? $this->response->route('school.teacher.student.create')
            : $this->response->route('school.teacher.student.index');
    }

    public function show(ShowStudentRequest $request)
    {
        return $this->view->make('school.teacher.student.show',
            $this->studentService->getProfile($request->id));
    }
}
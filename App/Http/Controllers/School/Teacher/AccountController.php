<?php

namespace App\Http\Controllers\School\Teacher;

use App\Domain\Services\AccountService;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdatePasswordRequest;
use App\Http\Requests\School\UpdatePersonalInfoRequest;
use Library\Http\Response;
use Library\Http\View;
use Library\Session\Session;

class AccountController extends Controller
{
    /**
     * @var AccountService
     */
    private $accountService;

    public function __construct(View $view, Session $session, Response $response, AccountService $accountService)
    {
        parent::__construct($view, $session, $response);

        $this->accountService = $accountService;
    }

    public function index()
    {
        return $this->view->make('school.teacher.account.index');
    }

    public function personalInfo()
    {
        return $this->view->make('school.teacher.account.personalInfo');
    }

    public function updatePersonalInfo(UpdatePersonalInfoRequest $request)
    {
        $this->accountService->updatePersonalInfo($request->all())
            ? $this->session->setFlash('Information updated!')
            : $this->session->setFlash('Could not update your information. Please try again.', 'error');

        $this->response->route('school.teacher.account.personalInformation');
    }

    public function password()
    {
        return $this->view->make('school.teacher.account.password');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->accountService->updatePassword($request->all())
            ? $this->session->setFlash('Password updated!')
            : $this->session->setFlash('Could not update password. Please try again.', 'error');

        $this->response->route('school.teacher.account.password');
    }
}
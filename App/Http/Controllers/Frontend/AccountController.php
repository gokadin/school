<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Services\LoginService;
use App\Domain\Services\TeacherRegistrationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Http\Requests\Frontend\RegistrationRequest;

class AccountController extends Controller
{
    public function index()
    {
        return $this->view->make('frontend.account.index');
    }

    public function signup()
    {
        return $this->view->make('frontend.account.signUp');
    }

    public function login(LoginRequest $request, LoginService $loginService)
    {
        if (!$loginService->login($request->all()))
        {
            return $this->response->route('frontend.account.index')
                ->withFlash('Incorrect login. Please try again.', 'error');
        }

        return $this->response->route('school.teacher.index.index');
    }

    public function logout(LoginService $loginService)
    {
        $loginService->logout();

        return $this->response->route('frontend.account.index');
    }

    public function resetPassword()
    {
        return $this->view->make('frontend.account.resetPassword');
    }

    public function preRegisterTeacher(PreRegistrationRequest $request,
                                       TeacherRegistrationService $teacherRegistrationService)
    {
        $teacherRegistrationService->preRegister($request->all());

        return $this->response->route('frontend.account.signUpLand');
    }

    public function emailConfirmation(TeacherRegistrationService $teacherRegistrationService, $id, $code)
    {
        $tempTeacher = $teacherRegistrationService->findTempTeacher($id, $code);

        if (!$tempTeacher)
        {
            return $this->response->route('frontend.account.signUp')
                ->withFlash('Your account no longer exists. Please sign up again.', 'error');
        }

        return $this->view->make('frontend.account.emailConfirmation', compact('tempTeacher'));
    }

    public function registerTeacher(RegistrationRequest $request, TeacherRegistrationService $teacherRegistrationService)
    {
        if (!$teacherRegistrationService->register($request->all()))
        {
            return $this->response->route('frontend.account.signUp')
                ->withFlash('Your account no longer exists. Please try signing up again.', 'error');
        }

        return $this->response->route('school.teacher.index.index');
    }

    public function signUpLand()
    {
        return $this->view->make('frontend.account.signUpLand');
    }
}
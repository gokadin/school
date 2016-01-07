<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Services\LoginService;
use App\Domain\Services\TeacherRegistrationService;
use App\Domain\Subscriptions\SubscriptionsTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Http\Requests\Frontend\RegistrationRequest;
use App\Jobs\Frontend\RegisterTeacher;
use App\Repositories\UserRepository;
use Library\Mail\Mail;

class AccountController extends Controller
{
    public function index()
    {
        return $this->view->make('frontend.account.index');
    }

    public function signup()
    {
        return $this->view->make('frontend.account.signUp', [
            'subscriptions' => SubscriptionsTypes::describeSubscriptions()
        ]);
    }

    public function login(LoginRequest $request, LoginService $loginService)
    {
        if (!$loginService->login($request->all()))
        {
            $this->session->setFlash('Incorrect login. Please try again.', 'error');
            $this->response->route('frontend.account.index');
        }

        $this->response->route('school.teacher.index.index');
    }

    public function logout(UserRepository $userRepository)
    {
        $userRepository->logout();

        $this->response->route('frontend.account.index');
    }

    public function resetPassword()
    {
        return $this->view->make('frontend.account.resetPassword');
    }

    public function preRegisterTeacher(PreRegistrationRequest $request,
                                       TeacherRegistrationService $teacherRegistrationService)
    {
        $teacherRegistrationService->preRegister($request->all());

        $this->response->route('frontend.account.signUpLand');
    }

    public function emailConfirmation(UserRepository $userRepository, $id, $code)
    {
        $tempTeacher = $userRepository->findTempTeacher($id);

        if ($tempTeacher == null || $tempTeacher->confirmationCode() != $code)
        {
            $this->session->setFlash('Your account no longer exists. Please sign up again.');
            $this->response->route('frontend.account.signUp');
        }

        return $this->view->make('frontend.account.emailConfirmation', compact('tempTeacher'));
    }

    public function registerTeacher(RegistrationRequest $request)
    {
        $this->dispatchJob(new RegisterTeacher($request->all()));

        $this->response->route('frontend.index.index');
    }

    public function signUpLand(Mail $mail)
    {
        $mail->send('test', [], function($m) {
            $m->to('guivio_147@hotmail.com');
            $m->from('givi.odikadze@gmail.com');
            $m->subject('some subject2');
        });

        return $this->view->make('frontend.account.signUpLand');
    }
}
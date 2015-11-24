<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Subscriptions\SubscriptionsTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Http\Requests\Frontend\RegistrationRequest;
use App\Jobs\Frontend\PreRegisterTeacher;
use Library\Facades\Sentry;
use Models\Student;
use Models\Teacher;
use App\Repositories\UserRepository;

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

    public function login(LoginRequest $request)
    {
        $teacher = Sentry::attempt(Teacher::class, [
            'email' => $request->email,
            'password' => md5($request->password)
        ]);

        if ($teacher != false)
        {
            $this->redirect->to('school.teacher.index.index');
            return;
        }

        $student = Sentry::attempt(Student::class, [
            'email' => $request->email,
            'password' => md5($request->password)
        ]);

        if ($student != false)
        {
            $this->redirect->to('school.student.index.index');
            return;
        }

        $this->session->setFlash('The email or password is incorrect. Please try again.');
        $this->redirect->back();
    }

    public function logout()
    {
        Sentry::logout();
        $this->redirect->to('frontend.index.index');
    }

    public function resetPassword()
    {
        return $this->view->make('frontend.account.resetPassword');
    }

    public function preRegisterTeacher(PreRegistrationRequest $request)
    {
        $this->dispatchJob(new PreRegisterTeacher($request->all()));

        $this->redirect->to('frontend.account.signUpLand');
    }

    public function emailConfirmation(UserRepository $userRepository, $id, $code)
    {
        $tempTeacher = $userRepository->findTempTeacher($id);

        if ($tempTeacher == null)
        {
            $this->session->setFlash('Your account no longer exists in our database');
            $this->redirect->to('frontend.account.signUp');
        }

        if ($tempTeacher->confirmationCode() != $code)
        {
            $this->session->setFlash('The confirmation code is invalid');
            $this->redirect->to('frontend.account.signUp');
        }

        return $this->view->make('frontend.account.emailConfirmation', compact('tempTeacher'));
    }

    public function registerTeacher(RegistrationRequest $request, UserRepository $userRepository)
    {
        $teacher = $userRepository->registerTeacher($request->all());
        if (!$teacher)
        {
            $this->session->setFlash('Your account no longer exists. Please try signing up again.');
            $this->redirect->to('frontend.account.signUp');
        }

        $userRepository->loginTeacher($teacher);

        $this->redirect->to('frontend.index.index');
    }

    public function signUpLand(Session $session)
    {
        return $this->view->make('frontend.account.signUpLand', ['confn' => $session->getFlash()]);
    }
}
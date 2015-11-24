<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Subscriptions\SubscriptionsTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Http\Requests\Frontend\RegistrationRequest;
use App\Jobs\Frontend\PreRegisterTeacher;
use App\Repositories\UserRepository;
use App\Domain\Users\Teacher;
use App\Domain\Users\Student;

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

    public function login(LoginRequest $request, UserRepository $userRepository)
    {
        $teacher = $userRepository->attemptLogin(Teacher::class, $request->email, md5($request->password));

        if ($teacher != false)
        {
            $this->response->route('school.teacher.index.index');
            return;
        }

        $student = $userRepository->attemptLogin(Student::class, $request->email, md5($request->password));

        if ($student != false)
        {
            $this->response->route('school.student.index.index');
            return;
        }

        $this->session->setFlash('The email or password is incorrect. Please try again.');
        $this->response->back();
    }

    public function logout(UserRepository $userRepository)
    {
        $userRepository->logout();
        $this->response->route('frontend.index.index');
    }

    public function resetPassword()
    {
        return $this->view->make('frontend.account.resetPassword');
    }

    public function preRegisterTeacher(PreRegistrationRequest $request)
    {
        $this->queue->push(new PreRegisterTeacher($request->all()));

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

    public function registerTeacher(RegistrationRequest $request, UserRepository $userRepository)
    {
        $teacher = $userRepository->registerTeacher($request->all());
        if (!$teacher)
        {
            $this->session->setFlash('Your account no longer exists. Please try signing up again.');
            $this->response->route('frontend.account.signUp');
        }

        $userRepository->loginTeacher($teacher);

        $this->response->route('frontend.index.index');
    }

    public function signUpLand(Session $session)
    {
        return $this->view->make('frontend.account.signUpLand', ['confn' => $session->getFlash()]);
    }
}
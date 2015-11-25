<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Subscriptions\SubscriptionsTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Http\Requests\Frontend\RegistrationRequest;
use App\Jobs\Frontend\LoginUser;
use App\Jobs\Frontend\PreRegisterTeacher;
use App\Jobs\Frontend\RegisterTeacher;
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
        $this->dispatchJob(new LoginUser($request->all()));
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

    public function preRegisterTeacher(PreRegistrationRequest $request)
    {
        $this->dispatchJob(new PreRegisterTeacher($request->all()));

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

    public function signUpLand(Session $session)
    {
        return $this->view->make('frontend.account.signUpLand', ['confn' => $session->getFlash()]);
    }
}
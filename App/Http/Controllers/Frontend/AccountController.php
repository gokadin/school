<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Library\Http\Request;
use App\Http\Requests\Frontend\PreRegistrationRequest;
use App\Http\Requests\Frontend\LoginRequest;
use App\Repositories\Contracts\IUserRepository;
use Library\Facades\Page;
use Library\Facades\Session;
use Library\Facades\Redirect;
use Library\Facades\Sentry;
use Models\Student;
use Models\Teacher;
use Models\TempTeacher;
use Models\Subscription;

class AccountController extends Controller
{
    public function index()
    {
        return view('frontend.account.index');
    }

    public function signup()
    {
        $memberships = Subscription::getMembershipsArray();

        return view('frontend.account.signUp', compact('memberships'));
    }

    public function login(LoginRequest $request)
    {
        $teacher = Sentry::attempt(Teacher::class, [
            'email' => $request->email,
            'password' => md5($request->password)
        ]);

        if ($teacher != false)
        {
            Redirect::to('school.teacher.index.index');
            return;
        }

        $student = Sentry::attempt(Student::class, [
            'email' => $request->email,
            'password' => md5($request->password)
        ]);

        if ($student != false)
        {
            Redirect::to('school.student.index.index');
            return;
        }

        Session::setFlash('The email or password is incorrect. Please try again.');
        Redirect::back();
    }

    public function logout()
    {
        Sentry::logout();
        Redirect::to('frontend.index.index');
    }

    public function resetPassword()
    {
        return view('frontend.account.resetPassword');
    }

    public function preRegisterTeacher(PreRegistrationRequest $request, IUserRepository $userRepository)
    {
        if (!$userRepository->preRegisterTeacher($request->all()))
        {
            Session::setFlash('An error occurred. Please try again.');
            Response::back();
        }

        // Event::fire(...); // send email

        Redirect::to('frontend.account.signUpLand');
    }

    public function emailConfirmation(IUserRepository $userRepository, $id, $code)
    {
        $tempTeacher = $userRepository->findTempTeacher($id);
        if ($tempTeacher == null)
        {
            Session::setFlash('Your account no longer exists in our database');
            Redirect::to('frontend.account.signUp');
        }

        if ($tempTeacher->confirmation_code != $code)
        {
            Session::setFlash('The confirmation code is invalid');
            Redirect::to('frontend.account.signUp');
        }

        return view('frontend.account.emailConfirmation', compact('tempTeacher'));
    }

    public function registerTeacher(RegistrationRequest $request, IUserRepository $userRepository)
    {
        $teacher = $userRepository->registerTeacher($request->tempTeacherId);
        if (!$teacher)
        {
            Session::setFlash('Your account no longer exists. Please try signing up again.');
            Redirect::to('frontend.account.signUp');
        }

//        Event::fire(TeacherHasRegistered::class,
//            $userRepository->findTempTeacher($request->tempTeacherId),
//            $teacher);

        Sentry::login($teacher->id, 'Teacher');
        Redirect::to('school.teacher.index.index');
    }

    public function signUpLand()
    {
        return view('frontend.account.signUpLand', ['confn' => Session::getFlash()]);
    }
}
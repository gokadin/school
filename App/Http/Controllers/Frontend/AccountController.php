<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('frontend.account.index');
    }

    public function signup()
    {
        $memberships = array();

        $memberships[] = [
            'name' => 'Basic',
            'price' => 'FREE',
            'numStudents' => 5,
            'storageSpace' => '1GB'
        ];

        $memberships[] = [
            'name' => 'Silver',
            'price' => '14.99 / month',
            'numStudents' => 20,
            'storageSpace' => '5GB'
        ];

        $memberships[] = [
            'name' => 'Gold',
            'price' => '25.99 / month',
            'numStudents' => 50,
            'storageSpace' => '7GB'
        ];

        $memberships[] = [
            'name' => 'Platinum',
            'price' => '39.99 / month',
            'numStudents' => 'unlimited',
            'storageSpace' => '10GB'
        ];

        return view('frontend.account.signup', compact('memberships'));
    }

    public function logout()
    {
        Session::logout();
        Response::toAction('Frontend#Index#index');
    }
}
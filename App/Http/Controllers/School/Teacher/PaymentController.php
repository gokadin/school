<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use Library\Facades\Sentry;

class PaymentController extends Controller
{
    public function index()
    {
        $activties = Sentry::user()->activities();
        foreach ($activties as &$activty)
        {
            $activty->students = $activty->students();
        }

        return view('school.teacher.payment.index', [
            'activities' => json_encode($activties)
        ]);
    }
}
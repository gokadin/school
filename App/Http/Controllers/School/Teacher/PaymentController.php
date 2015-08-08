<?php

namespace App\Http\Controllers\School\Teacher;

use App\Http\Controllers\Controller;
use App\Repositories\PaymentRepository;

class PaymentController extends Controller
{
    public function index(PaymentRepository $paymentRepository)
    {
        return view('school.teacher.payment.index', [
            'activities' => $paymentRepository->prepareJsonForIndex()
        ]);
    }
}
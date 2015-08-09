<?php

namespace App\Listeners\School;

use App\Events\School\StudentRegistered;
use App\Listeners\Listener;
use App\Repositories\PaymentRepository;
use Library\Queue\Queueable;
use Library\Queue\ShouldQueue;

class InitiatePaymentRecord extends Listener implements ShouldQueue
{
    use Queueable;

    public function handle(StudentRegistered $event, PaymentRepository $paymentRepository)
    {
        $paymentRepository->initiateNewStudentRecord($event->student());
    }
}
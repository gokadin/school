<?php

namespace App\Listeners\School;

use App\Events\School\StudentPreRegistered;
use Library\Events\Listener;
use Library\Events\ShouldQueue;
use Library\Mail\Mail;

class SendStudentPreRegistrationEmail extends Listener implements ShouldQueue
{
    /**
     * @var Mail
     */
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function handle(StudentPreRegistered $event)
    {
        $tempStudent = $event->tempStudent();

        $this->mail->send('school.emails.studentPreRegisteredEmail', compact('tempStudent'), function($m) use ($tempStudent) {
            $m->to($tempStudent->email());
            $m->from('mailgun@mg.instructioner.com', 'Givi Odikadze');
            $m->subject('Instructioner registration');
        });
    }
}
<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use Library\Events\Listener;
use Library\Events\ShouldQueue;
use Library\Mail\Mail;

class SendTeacherPreRegistrationEmail extends Listener implements ShouldQueue
{
    /**
     * @var Mail
     */
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function handle(TeacherPreRegistered $event)
    {
        $tempTeacher = $event->tempTeacher();

        $this->mail->send('frontend.emails.teacherPreRegisteredEmail', compact('tempTeacher'), function($m) use ($tempTeacher) {
            $m->to($tempTeacher->email());
            $m->from('postmaster@instructioner.com');
            $m->subject('Instructioner account confirmation');
        });
    }
}
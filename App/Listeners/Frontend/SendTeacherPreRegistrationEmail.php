<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherPreRegistered;
use App\Listeners\Listener;
use Library\Mail\Mail;
use Library\Queue\ShouldQueue;

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
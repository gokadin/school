<?php

namespace App\Listeners\Frontend;

use App\Events\Frontend\TeacherRegistered;
use Library\Events\Listener;
use Library\Events\ShouldQueue;
use Library\Mail\Mail;

class SendTeacherRegistrationEmail extends Listener implements ShouldQueue
{
    /**
     * @var Mail
     */
    private $mail;

    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    public function handle(TeacherRegistered $event)
    {
        $teacher = $event->teacher();

        $this->mail->send('frontend.emails.teacherRegisteredEmail', compact('teacher'), function($m) use ($teacher) {
            $m->to($teacher->email());
            $m->from('mailgun@mg.instructioner.com', 'Givi Odikadze');
            $m->subject('Instructioner registration complete');
        });
    }
}
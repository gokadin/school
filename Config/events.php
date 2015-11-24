<?php

return [

    App\Events\Frontend\TeacherPreRegistered::class => [
        App\Events\Frontend\SendPreRegistrationEmail::class
    ],

    App\Events\Frontend\TeacherRegistered::class => [
        App\Events\Frontend\LoginTeacher::class,
        App\Events\Frontend\SendRegistrationEmail::class
    ],

    App\Events\School\StudentRegistered::class => [
        App\Events\School\InitiatePaymentRecord::class
    ]

];
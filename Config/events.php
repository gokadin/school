<?php

return [

    App\Events\Frontend\TeacherPreRegistered::class => [
        App\Listeners\Frontend\SendPreRegistrationEmail::class
    ],

    App\Events\Frontend\TeacherRegistered::class => [
        App\Listeners\Frontend\LoginTeacher::class,
        App\Listeners\Frontend\SendRegistrationEmail::class
    ],

    App\Events\Frontend\TeacherLoggedIn::class => [
        App\Listeners\Frontend\LogTeacherLogin::class
    ]

];
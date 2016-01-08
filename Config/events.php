<?php

return [

    App\Events\Frontend\TeacherPreRegistered::class => [
        App\Listeners\Frontend\SendTeacherPreRegistrationEmail::class
    ],

    App\Events\Frontend\TeacherRegistered::class => [
        App\Listeners\Frontend\SendTeacherRegistrationEmail::class
    ],

    App\Events\Frontend\UserLoggedIn::class => [
        App\Listeners\Frontend\LogUserLogin::class
    ],

    App\Events\School\StudentPreRegistered::class => [
        App\Listeners\School\SendStudentPreRegistrationEmail::class
    ]

];